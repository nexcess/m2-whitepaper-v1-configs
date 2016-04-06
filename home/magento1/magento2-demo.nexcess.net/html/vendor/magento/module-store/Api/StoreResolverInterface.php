<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Store\Api;

/**
 * Store resolver interface
 *
 * @api
 */
interface StoreResolverInterface
{
    /**
     * Param name
     */
    const PARAM_NAME = '___store';

    /**
     * Retrieve current store id
     *
     * @return string
     */
    public function getCurrentStoreId();
}
