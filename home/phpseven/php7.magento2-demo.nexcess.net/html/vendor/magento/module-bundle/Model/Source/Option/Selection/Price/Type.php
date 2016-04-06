<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Bundle\Model\Source\Option\Selection\Price;

use Magento\Bundle\Api\Data\LinkInterface;

/**
 * Extended Attributes Source Model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => LinkInterface::PRICE_TYPE_FIXED, 'label' => __('Fixed')],
            ['value' => LinkInterface::PRICE_TYPE_PERCENT, 'label' => __('Percent')]
        ];
    }
}
