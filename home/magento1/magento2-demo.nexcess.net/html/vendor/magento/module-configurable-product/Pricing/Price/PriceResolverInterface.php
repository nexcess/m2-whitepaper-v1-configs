<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableProduct\Pricing\Price;

interface PriceResolverInterface
{
    /**
     * @param \Magento\Framework\Pricing\SaleableInterface $product
     * @return float
     */
    public function resolvePrice(\Magento\Framework\Pricing\SaleableInterface $product);
}
