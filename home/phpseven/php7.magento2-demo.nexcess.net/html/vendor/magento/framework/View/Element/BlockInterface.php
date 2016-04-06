<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\View\Element;

/**
 * Magento Block
 *
 * Used to present information to user
 */
interface BlockInterface
{
    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml();
}
