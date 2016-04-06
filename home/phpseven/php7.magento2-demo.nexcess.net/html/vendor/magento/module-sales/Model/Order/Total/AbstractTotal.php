<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Model\Order\Total;

/**
 * Base class for configure totals order
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractTotal extends \Magento\Framework\DataObject
{
    /**
     * Process model configuration array.
     * This method can be used for changing models apply sort order
     *
     * @param   array $config
     * @return  array
     */
    public function processConfigArray($config)
    {
        return $config;
    }
}
