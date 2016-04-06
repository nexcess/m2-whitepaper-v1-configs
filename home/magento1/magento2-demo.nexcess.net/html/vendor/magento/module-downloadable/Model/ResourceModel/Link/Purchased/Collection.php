<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Model\ResourceModel\Link\Purchased;

/**
 * Downloadable links purchased resource collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\Downloadable\Model\Link\Purchased',
            'Magento\Downloadable\Model\ResourceModel\Link\Purchased'
        );
    }

    /**
     * Add purchased items to collection
     *
     * @return $this
     */
    public function addPurchasedItemsToResult()
    {
        $this->getSelect()->join(
            ['pi' => $this->getTable('downloadable_link_purchased_item')],
            'pi.purchased_id=main_table.purchased_id'
        );
        return $this;
    }
}
