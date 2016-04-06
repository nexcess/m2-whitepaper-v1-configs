<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class LastOrderedItems implements SectionSourceInterface
{
    /**
     * Limit of orders in side bar
     */
    const SIDEBAR_ORDER_LIMIT = 5;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $orders;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderConfig = $orderConfig;
        $this->_customerSession = $customerSession;
        $this->stockRegistry = $stockRegistry;
        $this->_storeManager = $storeManager;
    }

    /**
     * Init customer order for display on front
     *
     * @return void
     */
    protected function initOrders()
    {
        $customerId = $this->_customerSession->getCustomerId();

        $orders = $this->_orderCollectionFactory->create()
            ->addAttributeToFilter('customer_id', $customerId)
            ->addAttributeToFilter('status', ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()])
            ->addAttributeToSort('created_at', 'desc')
            ->setPage(1, 1);
        //TODO: add filter by current website
        $this->orders = $orders;
    }

    /**
     * Get list of last ordered products
     *
     * @return array
     */
    protected function getItems()
    {
        $items = [];
        $order = $this->getLastOrder();
        $limit = self::SIDEBAR_ORDER_LIMIT;

        if ($order) {
            $website = $this->_storeManager->getStore()->getWebsiteId();
            foreach ($order->getParentItemsRandomCollection($limit) as $item) {
                if ($item->getProduct() && in_array($website, $item->getProduct()->getWebsiteIds())) {
                    $items[] = [
                        'id' => $item->getId(),
                        'name' => $item->getName(),
                        'url' => $item->getProduct()->getProductUrl(),
                        'is_saleable' => $this->isItemAvailableForReorder($item),
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * Check item product availability for reorder
     *
     * @param  \Magento\Sales\Model\Order\Item $orderItem
     * @return boolean
     */
    protected function isItemAvailableForReorder(\Magento\Sales\Model\Order\Item $orderItem)
    {
        try {
            $stockItem = $this->stockRegistry->getStockItem(
                $orderItem->getProduct()->getId(),
                $orderItem->getStore()->getWebsiteId()
            );
            return $stockItem->getIsInStock();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $noEntityException) {
            return false;
        }
    }

    /**
     * Last order getter
     *
     * @return \Magento\Sales\Model\Order|void
     */
    protected function getLastOrder()
    {
        if (!$this->orders) {
            $this->initOrders();
        }
        foreach ($this->orders as $order) {
            return $order;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        return ['items' => $this->getItems()];
    }
}
