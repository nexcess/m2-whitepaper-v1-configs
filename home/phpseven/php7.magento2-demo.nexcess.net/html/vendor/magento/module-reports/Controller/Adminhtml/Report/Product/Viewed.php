<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Product;

use Magento\Reports\Model\Flag;

class Viewed extends \Magento\Reports\Controller\Adminhtml\Report\Product
{
    /**
     * Check is allowed for report
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Reports::viewed');
    }

    /**
     * Most viewed products
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_showLastExecutionTime(Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE, 'viewed');

            $this->_initAction()->_setActiveMenu(
                'Magento_Reports::report_products_viewed'
            )->_addBreadcrumb(
                __('Products Most Viewed Report'),
                __('Products Most Viewed Report')
            );
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Product Views Report'));

            $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_product_viewed.grid');
            $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

            $this->_initReportAction([$gridBlock, $filterFormBlock]);

            $this->_view->renderLayout();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('An error occurred while showing the product views report. Please review the log and try again.')
            );
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->_redirect('reports/*/viewed/');
            return;
        }
    }
}
