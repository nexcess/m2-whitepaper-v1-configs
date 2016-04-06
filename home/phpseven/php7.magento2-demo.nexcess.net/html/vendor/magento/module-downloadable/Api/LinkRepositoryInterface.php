<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Api;

use Magento\Downloadable\Api\Data\LinkInterface;

/**
 * Interface LinkRepositoryInterface
 * @package Magento\Downloadable\Api
 */
interface LinkRepositoryInterface
{
    /**
     * List of links with associated samples
     *
     * @param string $sku
     * @return \Magento\Downloadable\Api\Data\LinkInterface[]
     */
    public function getList($sku);

    /**
     * List of links with associated samples
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Downloadable\Api\Data\LinkInterface[]
     */
    public function getLinksByProduct(\Magento\Catalog\Api\Data\ProductInterface $product);

    /**
     * Update downloadable link of the given product (link type and its resources cannot be changed)
     *
     * @param string $sku
     * @param \Magento\Downloadable\Api\Data\LinkInterface $link
     * @param bool $isGlobalScopeContent
     * @return int
     */
    public function save($sku, LinkInterface $link, $isGlobalScopeContent = true);

    /**
     * Delete downloadable link
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);
}
