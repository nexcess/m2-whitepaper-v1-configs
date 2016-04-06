<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Model\Menu\Builder\Command;

/**
 * Command to update menu item data
 */
class Update extends \Magento\Backend\Model\Menu\Builder\AbstractCommand
{
    /**
     * Update item data
     *
     * @param array $itemParams
     * @return array
     */
    protected function _execute(array $itemParams)
    {
        foreach ($this->_data as $key => $value) {
            $itemParams[$key] = $value;
        }
        return $itemParams;
    }
}
