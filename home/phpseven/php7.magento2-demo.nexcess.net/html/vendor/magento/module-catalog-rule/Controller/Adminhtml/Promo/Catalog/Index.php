<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog;

class Index extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $dirtyRules = $this->_objectManager->create('Magento\CatalogRule\Model\Flag')->loadSelf();
        if ($dirtyRules->getState()) {
            $this->messageManager->addNotice($this->getDirtyRulesNoticeMessage());
        }

        $this->_initAction()->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Catalog Price Rule'));
        $this->_view->renderLayout();
    }
}
