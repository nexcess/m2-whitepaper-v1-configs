<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Config edit page
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Config\Block\System\Config;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Edit extends \Magento\Backend\Block\Widget
{
    const DEFAULT_SECTION_BLOCK = 'Magento\Config\Block\System\Config\Form';

    /**
     * Form block class name
     *
     * @var string
     */
    protected $_formBlockName;

    /**
     * Block template File
     *
     * @var string
     */
    protected $_template = 'Magento_Config::system/config/edit.phtml';

    /**
     * Configuration structure
     *
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $_configStructure;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure,
        array $data = []
    ) {
        $this->_configStructure = $configStructure;
        parent::__construct($context, $data);
    }

    /**
     * Prepare layout object
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        /** @var $section \Magento\Config\Model\Config\Structure\Element\Section */
        $section = $this->_configStructure->getElement($this->getRequest()->getParam('section'));
        $this->_formBlockName = $section->getFrontendModel();
        if (empty($this->_formBlockName)) {
            $this->_formBlockName = self::DEFAULT_SECTION_BLOCK;
        }
        $this->setTitle($section->getLabel());
        $this->setHeaderCss($section->getHeaderCss());

        $this->getToolbar()->addChild(
            'save_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'id' => 'save',
                'label' => __('Save Config'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#config-edit-form']],
                ]
            ]
        );
        $block = $this->getLayout()->createBlock($this->_formBlockName);
        $this->setChild('form', $block);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve rendered save buttons
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve config save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/system_config/save', ['_current' => true]);
    }
}
