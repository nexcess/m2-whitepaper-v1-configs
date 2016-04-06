<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Related;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Related products grid
 */
class Grid extends GridInterface
{
    /**
     * Grid fields map
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => 'input[name="name"]',
        ],
        'sku' => [
            'selector' => 'input[name="sku"]',
        ],
        'type' => [
            'selector' => 'select[name="type"]',
            'input' => 'select',
        ],
    ];
}
