<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;

class NewAction extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @var Initialization\StockDataFilter
     */
    protected $stockFilter;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Action\Context $context
     * @param Builder $productBuilder
     * @param Initialization\StockDataFilter $stockFilter
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        Initialization\StockDataFilter $stockFilter,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->stockFilter = $stockFilter;
        parent::__construct($context, $productBuilder);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Create new product page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('set')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $product = $this->productBuilder->build($this->getRequest());

        $productData = $this->getRequest()->getPost('product');
        if (!$productData) {
            $sessionData = $this->_session->getProductData(true);
            if (!empty($sessionData['product'])) {
                $productData = $sessionData['product'];
            }
        }
        if ($productData) {
            $stockData = isset($productData['stock_data']) ? $productData['stock_data'] : [];
            $productData['stock_data'] = $this->stockFilter->filter($stockData);
            $product->addData($productData);
        }

        $this->_eventManager->dispatch('catalog_product_new_action', ['product' => $product]);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if ($this->getRequest()->getParam('popup')) {
            $resultPage->addHandle(['popup', 'catalog_product_' . $product->getTypeId()]);
        } else {
            $resultPage->addHandle(['catalog_product_' . $product->getTypeId()]);
            $resultPage->setActiveMenu('Magento_Catalog::catalog_products');
            $resultPage->getConfig()->getTitle()->prepend(__('Products'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Product'));
        }

        $block = $resultPage->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        return $resultPage;
    }
}
