<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\User\Block;

class Buttons extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'backButton',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Back'),
                'onclick' => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
                'class' => 'back'
            ]
        );

        $this->getToolbar()->addChild(
            'resetButton',
            'Magento\Backend\Block\Widget\Button',
            ['label' => __('Reset'), 'onclick' => 'window.location.reload()', 'class' => 'reset']
        );

        if (intval($this->getRequest()->getParam('rid'))) {
            $this->getToolbar()->addChild(
                'deleteButton',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Delete Role'),
                    'onclick' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getUrl(
                        '*/*/delete',
                        ['rid' => $this->getRequest()->getParam('rid')]
                    ) . '\')',
                    'class' => 'delete'
                ]
            );
        }

        $this->getToolbar()->addChild(
            'saveButton',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Save Role'),
                'class' => 'save primary save-role',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#role-edit-form']],
                ]
            ]
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    /**
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    /**
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    /**
     * @return string|void
     */
    public function getDeleteButtonHtml()
    {
        if (intval($this->getRequest()->getParam('rid')) == 0) {
            return;
        }
        return $this->getChildHtml('deleteButton');
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->_coreRegistry->registry('user_data');
    }
}
