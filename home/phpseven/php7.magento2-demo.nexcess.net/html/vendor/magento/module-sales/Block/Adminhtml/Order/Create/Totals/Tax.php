<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Totals;

/**
 * Tax Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Tax extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals\DefaultTotals
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/create/totals/tax.phtml';
}
