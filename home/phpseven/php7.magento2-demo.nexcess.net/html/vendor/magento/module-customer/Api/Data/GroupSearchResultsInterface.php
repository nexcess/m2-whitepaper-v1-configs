<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Api\Data;

/**
 * Interface for customer groups search results.
 */
interface GroupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get customer groups list.
     *
     * @api
     * @return \Magento\Customer\Api\Data\GroupInterface[]
     */
    public function getItems();

    /**
     * Set customer groups list.
     *
     * @api
     * @param \Magento\Customer\Api\Data\GroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
