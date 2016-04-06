<?php
/**
 * Root ACL Resource
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Acl;

class RootResource
{
    /**
     * Root resource id
     *
     * @var string
     */
    protected $_identifier;

    /**
     * @param string $identifier
     */
    public function __construct($identifier)
    {
        $this->_identifier = $identifier;
    }

    /**
     * Retrieve root resource id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_identifier;
    }
}
