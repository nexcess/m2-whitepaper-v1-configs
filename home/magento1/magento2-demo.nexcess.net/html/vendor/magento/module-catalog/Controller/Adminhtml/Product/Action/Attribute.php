<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Catalog\Controller\Adminhtml\Product\Action;

use Magento\Backend\App\Action;

/**
 * Adminhtml catalog product action attribute update controller
 */
abstract class Attribute extends Action
{
    /**
     *  @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute
     */
    protected $attributeHelper;

    /**
     * @param Action\Context $context
     * @param \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper
     */
    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper
    ) {
        parent::__construct($context);
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * Validate selection of products for mass update
     *
     * @return boolean
     */
    protected function _validateProducts()
    {
        $error = false;
        $productIds = $this->attributeHelper->getProductIds();
        if (!is_array($productIds)) {
            $error = __('Please select products for attributes update.');
        } elseif (!$this->_objectManager->create('Magento\Catalog\Model\Product')->isProductsHasSku($productIds)) {
            $error = __('Please make sure to define SKU values for all processed products.');
        }

        if ($error) {
            $this->messageManager->addError($error);
        }

        return !$error;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::update_attributes');
    }
}
