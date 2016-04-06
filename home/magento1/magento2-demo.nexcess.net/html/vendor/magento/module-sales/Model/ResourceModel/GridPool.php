<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\ResourceModel;

/**
 * Class GridPool
 */
class GridPool
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Grid[]
     */
    protected $grids;

    /**
     * @param array $grids
     */
    public function __construct(array $grids)
    {
        $this->grids = $grids;
    }

    /**
     * Refresh grids list
     *
     * @param int $orderId
     * @return $this
     */
    public function refreshByOrderId($orderId)
    {
        foreach ($this->grids as $grid) {
            $grid->refresh($orderId, $grid->getOrderIdField());
        }

        return $this;
    }
}
