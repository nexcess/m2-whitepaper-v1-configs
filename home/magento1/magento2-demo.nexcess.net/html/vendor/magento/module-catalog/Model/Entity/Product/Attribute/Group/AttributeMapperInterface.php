<?php
/**
 * Attribute mapper that is used to build frontend representation of attribute
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Entity\Product\Attribute\Group;

use Magento\Eav\Model\Entity\Attribute;

interface AttributeMapperInterface
{
    /**
     * Map attribute to presentation format
     *
     * @param Attribute $attribute
     * @return array
     */
    public function map(Attribute $attribute);
}
