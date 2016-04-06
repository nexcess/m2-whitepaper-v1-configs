<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Helper\Dashboard;

/**
 * Adminhtml dashboard helper for orders
 */
class Order extends \Magento\Backend\Helper\Dashboard\AbstractDashboard
{
    /**
     * @var \Magento\Reports\Model\ResourceModel\Order\Collection
     */
    protected $_orderCollection;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Reports\Model\ResourceModel\Order\Collection $orderCollection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Reports\Model\ResourceModel\Order\Collection $orderCollection
    ) {
        $this->_orderCollection = $orderCollection;
        parent::__construct(
            $context
        );
    }

    /**
     * @return void
     */
    protected function _initCollection()
    {
        $isFilter = $this->getParam('store') || $this->getParam('website') || $this->getParam('group');

        $this->_collection = $this->_orderCollection->prepareSummary($this->getParam('period'), 0, 0, $isFilter);

        if ($this->getParam('store')) {
            $this->_collection->addFieldToFilter('store_id', $this->getParam('store'));
        } elseif ($this->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', ['in' => implode(',', $storeIds)]);
        } elseif ($this->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', ['in' => implode(',', $storeIds)]);
        } elseif (!$this->_collection->isLive()) {
            $this->_collection->addFieldToFilter(
                'store_id',
                ['eq' => $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId()]
            );
        }
        $this->_collection->load();
    }
}
