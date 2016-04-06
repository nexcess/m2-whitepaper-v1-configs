<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Product alerts tab
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab;

class Alerts extends \Magento\Backend\Block\Widget\Tab
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/tab/alert.phtml';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Accordion')->setId('productAlerts');
        /* @var $accordion \Magento\Backend\Block\Widget\Accordion */

        $alertPriceAllow = $this->_scopeConfig->getValue('catalog/productalert/allow_price', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $alertStockAllow = $this->_scopeConfig->getValue('catalog/productalert/allow_stock', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($alertPriceAllow) {
            $accordion->addItem(
                'price',
                [
                    'title' => __('We saved the price alert subscription.'),
                    'content' => $this->getLayout()->createBlock(
                        'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts\Price'
                    )->toHtml() . '<br />',
                    'open' => true
                ]
            );
        }
        if ($alertStockAllow) {
            $accordion->addItem(
                'stock',
                [
                    'title' => __('We saved the stock notification.'),
                    'content' => $this->getLayout()->createBlock(
                        'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts\Stock'
                    ),
                    'open' => true
                ]
            );
        }

        $this->setChild('accordion', $accordion);

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }

    /**
     * Tab is hidden
     *
     * @return bool
     */
    public function canShowTab()
    {
        $alertPriceAllow = $this->_scopeConfig->getValue('catalog/productalert/allow_price', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $alertStockAllow = $this->_scopeConfig->getValue('catalog/productalert/allow_stock', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return ($alertPriceAllow || $alertStockAllow) && parent::canShowTab();
    }
}
