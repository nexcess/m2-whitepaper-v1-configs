<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog product abstract price backend attribute model with customer group specific
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\GroupPrice;

abstract class AbstractGroupPrice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Load Tier Prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @return array
     */
    public function loadPriceData($productId, $websiteId = null)
    {
        $connection = $this->getConnection();

        $columns = [
            'price_id' => $this->getIdFieldName(),
            'website_id' => 'website_id',
            'all_groups' => 'all_groups',
            'cust_group' => 'customer_group_id',
            'price' => 'value',
        ];

        $columns = $this->_loadPriceDataColumns($columns);

        $select = $connection->select()->from($this->getMainTable(), $columns)->where('entity_id=?', $productId);

        $this->_loadPriceDataSelect($select);

        if ($websiteId !== null) {
            if ($websiteId == '0') {
                $select->where('website_id = ?', $websiteId);
            } else {
                $select->where('website_id IN(?)', [0, $websiteId]);
            }
        }

        return $connection->fetchAll($select);
    }

    /**
     * Load specific sql columns
     *
     * @param array $columns
     * @return array
     */
    protected function _loadPriceDataColumns($columns)
    {
        return $columns;
    }

    /**
     * Load specific db-select data
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _loadPriceDataSelect($select)
    {
        return $select;
    }

    /**
     * Delete Tier Prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @param int $priceId
     * @return int The number of affected rows
     */
    public function deletePriceData($productId, $websiteId = null, $priceId = null)
    {
        $connection = $this->getConnection();

        $conds = [$connection->quoteInto('entity_id = ?', $productId)];

        if ($websiteId !== null) {
            $conds[] = $connection->quoteInto('website_id = ?', $websiteId);
        }

        if ($priceId !== null) {
            $conds[] = $connection->quoteInto($this->getIdFieldName() . ' = ?', $priceId);
        }

        $where = implode(' AND ', $conds);

        return $connection->delete($this->getMainTable(), $where);
    }

    /**
     * Save tier price object
     *
     * @param \Magento\Framework\DataObject $priceObject
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice
     */
    public function savePriceData(\Magento\Framework\DataObject $priceObject)
    {
        $connection = $this->getConnection();
        $data = $this->_prepareDataForTable($priceObject, $this->getMainTable());

        if (!empty($data[$this->getIdFieldName()])) {
            $where = $connection->quoteInto($this->getIdFieldName() . ' = ?', $data[$this->getIdFieldName()]);
            unset($data[$this->getIdFieldName()]);
            $connection->update($this->getMainTable(), $data, $where);
        } else {
            $connection->insert($this->getMainTable(), $data);
        }
        return $this;
    }
}
