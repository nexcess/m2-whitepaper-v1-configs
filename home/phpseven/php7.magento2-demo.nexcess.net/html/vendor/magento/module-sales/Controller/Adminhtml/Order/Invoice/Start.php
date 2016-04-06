<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Invoice;

class Start extends \Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice\View
{
    /**
     * Start create invoice action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /**
         * Clear old values for invoice qty's
         */
        $this->_getSession()->getInvoiceItemQtys(true);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
        return $resultRedirect;
    }
}
