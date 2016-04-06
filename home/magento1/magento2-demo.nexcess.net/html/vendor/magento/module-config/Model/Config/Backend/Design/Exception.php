<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Config\Model\Config\Backend\Design;

class Exception extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'core_config_backend_design_exception';
}
