<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Framework\Pricing\Price\BasePriceProviderInterface;

/**
 * Class RegularPrice
 */
class RegularPrice extends AbstractPrice implements BasePriceProviderInterface
{
    /**
     * Price type
     */
    const PRICE_CODE = 'regular_price';

    /**
     * Get price value
     *
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value === null) {
            $price = $this->product->getPrice();
            $priceInCurrentCurrency = $this->priceCurrency->convertAndRound($price);
            $this->value = $priceInCurrentCurrency ? floatval($priceInCurrentCurrency) : false;
        }
        return $this->value;
    }
}
