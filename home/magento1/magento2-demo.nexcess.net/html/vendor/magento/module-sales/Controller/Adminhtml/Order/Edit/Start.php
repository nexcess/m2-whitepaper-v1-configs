<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Edit;

class Start extends \Magento\Sales\Controller\Adminhtml\Order\Create\Start
{
    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::actions_edit');
    }

    /**
     * Start edit order initialization
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->_getSession()->clearStorage();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            if ($order->getId()) {
                $this->_getSession()->setUseOldShippingMethod(true);
                $this->_getOrderCreateModel()->initFromOrder($order);
                $resultRedirect->setPath('sales/*');
            } else {
                $resultRedirect->setPath('sales/order/');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, $e->getMessage());
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }
        return $resultRedirect;
    }
}
