<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ObjectManager\TestAsset;

class InterfaceInjection
{
    /**
     * @var \Magento\Framework\ObjectManager\TestAsset\TestAssetInterface
     */
    protected $_object;

    /**
     * @param \Magento\Framework\ObjectManager\TestAsset\TestAssetInterface $object
     */
    public function __construct(\Magento\Framework\ObjectManager\TestAsset\TestAssetInterface $object)
    {
        $this->_object = $object;
    }
}
