<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eav\Api\Data;

interface AttributeGroupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attribute sets list.
     *
     * @return \Magento\Eav\Api\Data\AttributeGroupInterface[]
     */
    public function getItems();

    /**
     * Set attribute sets list.
     *
     * @param \Magento\Eav\Api\Data\AttributeGroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
