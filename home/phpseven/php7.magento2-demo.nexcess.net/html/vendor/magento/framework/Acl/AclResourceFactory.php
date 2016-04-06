<?php
/**
 * Factory for Acl resource
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Acl;

use Magento\Framework\ObjectManagerInterface;

class AclResourceFactory
{
    const RESOURCE_CLASS_NAME = 'Magento\Framework\Acl\AclResource';

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return new ACL resource model
     *
     * @param array $arguments
     * @return AclResource
     */
    public function createResource(array $arguments = [])
    {
        return $this->_objectManager->create(self::RESOURCE_CLASS_NAME, $arguments);
    }
}
