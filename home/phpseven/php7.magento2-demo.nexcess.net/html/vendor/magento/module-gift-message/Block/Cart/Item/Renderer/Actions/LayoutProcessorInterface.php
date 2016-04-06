<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftMessage\Block\Cart\Item\Renderer\Actions;

use Magento\Quote\Model\Quote\Item\AbstractItem;

interface LayoutProcessorInterface
{
    /**
     * Process JS layout of block
     *
     * @param array $jsLayout
     * @param AbstractItem $item
     * @return array
     */
    public function process($jsLayout, AbstractItem $item);
}
