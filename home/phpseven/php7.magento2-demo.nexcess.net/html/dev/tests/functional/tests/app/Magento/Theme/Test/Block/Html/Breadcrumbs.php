<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Test\Block\Html;

use Magento\Mtf\Block\Block;

/**
 * Page breadcrumbs block.
 */
class Breadcrumbs extends Block
{
    /**
     * Get breadcrumbs content of current page.
     *
     * @return string
     */
    public function getText()
    {
        return $this->_rootElement->getText();
    }
}
