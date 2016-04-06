<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Wishlist sidebar block
 */
namespace Magento\Wishlist\Block\Customer;

class Sidebar extends \Magento\Wishlist\Block\AbstractBlock
{
    /**
     * Retrieve block title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __('My Wish List');
    }
}
