<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Model\Product\TypeHandler;

use Magento\Catalog\Model\Product;

/**
 * Interface TypeHandlerInterface
 */
interface TypeHandlerInterface
{
    /**
     * @param Product $product
     * @param array $data
     * @return void
     */
    public function save(Product $product, array $data);

    /**
     * @param array $data
     * @return bool
     */
    public function isCanHandle(array $data);
}
