<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogInventory\Api;

/**
 * Interface StockIndexInterface
 * @api
 */
interface StockIndexInterface
{
    /**
     * Rebuild stock index of the given scope
     *
     * @param int $productId
     * @param int $scopeId
     * @return bool
     */
    public function rebuild($productId = null, $scopeId = null);
}
