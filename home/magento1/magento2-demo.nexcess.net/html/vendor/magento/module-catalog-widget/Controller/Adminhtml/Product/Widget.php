<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogWidget\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;

/**
 * Class Widget
 */
abstract class Widget extends Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Widget::widget_instance');
    }
}
