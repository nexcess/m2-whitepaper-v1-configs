<?php
/**
 * Google Optmizer Product Block
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\GoogleOptimizer\Block\Code;

class Product extends \Magento\GoogleOptimizer\Block\AbstractCode implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var Product name in registry
     */
    protected $_registryName = 'current_product';

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->_getEntity()->getIdentities();
    }
}
