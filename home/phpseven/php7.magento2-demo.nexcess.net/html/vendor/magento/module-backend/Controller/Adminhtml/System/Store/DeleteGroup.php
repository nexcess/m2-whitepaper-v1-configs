<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Controller\Adminhtml\System\Store;

class DeleteGroup extends \Magento\Backend\Controller\Adminhtml\System\Store
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam('item_id', null);
        if (!($model = $this->_objectManager->create('Magento\Store\Model\Group')->load($itemId))) {
            $this->messageManager->addError(__('Something went wrong. Please try again.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/*/');
        }
        if (!$model->isCanDelete()) {
            $this->messageManager->addError(__('This store cannot be deleted.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/*/editGroup', ['group_id' => $itemId]);
        }

        $this->_addDeletionNotice('store');

        $resultPage = $this->createPage();
        $resultPage->addBreadcrumb(__('Delete Store'), __('Delete Store'))
            ->addContent(
                $resultPage->getLayout()->createBlock('Magento\Backend\Block\System\Store\Delete')
                    ->setFormActionUrl($this->getUrl('adminhtml/*/deleteGroupPost'))
                    ->setBackUrl($this->getUrl('adminhtml/*/editGroup', ['group_id' => $itemId]))
                    ->setStoreTypeTitle(__('Store'))
                    ->setDataObject($model)
            );
        $resultPage->getConfig()->getTitle()->prepend(__('Delete Store'));
        return $resultPage;
    }
}
