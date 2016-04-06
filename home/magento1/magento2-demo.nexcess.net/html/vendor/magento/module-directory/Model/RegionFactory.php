<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Region factory
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model;

class RegionFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new region model
     *
     * @param array $arguments
     * @return \Magento\Directory\Model\Region
     */
    public function create(array $arguments = [])
    {
        return $this->_objectManager->create('Magento\Directory\Model\Region', $arguments);
    }
}
