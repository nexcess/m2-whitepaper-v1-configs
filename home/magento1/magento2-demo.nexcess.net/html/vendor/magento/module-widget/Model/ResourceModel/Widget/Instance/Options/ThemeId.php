<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Widget Instance Theme Id Options
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Model\ResourceModel\Widget\Instance\Options;

class ThemeId implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Widget\Model\Widget\Instance
     */
    protected $_resourceModel;

    /**
     * @param \Magento\Theme\Model\ResourceModel\Theme\Collection $widgetResourceModel
     */
    public function __construct(\Magento\Theme\Model\ResourceModel\Theme\Collection $widgetResourceModel)
    {
        $this->_resourceModel = $widgetResourceModel;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_resourceModel->toOptionHash();
    }
}
