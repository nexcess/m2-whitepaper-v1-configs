<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRule\Controller\Adminhtml\Promo\Quote;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportCouponsCsv extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    /**
     * Export coupon codes as CSV file
     *
     * @return \Magento\Framework\App\ResponseInterface|null
     */
    public function execute()
    {
        $this->_initRule();
        $rule = $this->_coreRegistry->registry('current_promo_quote_rule');
        if ($rule->getId()) {
            $fileName = 'coupon_codes.csv';
            $content = $this->_view->getLayout()->createBlock(
                'Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Grid'
            )->getCsvFile();
            return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
        } else {
            $this->_redirect('sales_rule/*/detail', ['_current' => true]);
            return;
        }
    }
}
