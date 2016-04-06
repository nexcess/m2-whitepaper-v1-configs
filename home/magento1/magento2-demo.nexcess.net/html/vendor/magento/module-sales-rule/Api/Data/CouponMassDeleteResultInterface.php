<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRule\Api\Data;

/**
 * Coupon mass delete results interface.
 *
 * @api
 */
interface CouponMassDeleteResultInterface
{
    /**
     * Get list of failed items.
     *
     * @return string[]
     */
    public function getFailedItems();

    /**
     * Set list of failed items.
     *
     * @param string[] $items
     * @return $this
     */
    public function setFailedItems(array $items);

    /**
     * Get list of missing items.
     *
     * @return string[]
     */
    public function getMissingItems();

    /**
     * Set list of missing items.
     *
     * @param string[] $items
     * @return $this
     */
    public function setMissingItems(array $items);
}
