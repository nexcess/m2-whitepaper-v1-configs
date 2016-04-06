<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav;

/**
 * Catalog Product Eav Decimal Attributes Indexer resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Decimal extends AbstractEav
{
    /**
     * Initialize connection and define main index table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_index_eav_decimal', 'entity_id');
    }

    /**
     * Prepare data index for indexable attributes
     *
     * @param array $entityIds the entity ids limitation
     * @param int $attributeId the attribute id limitation
     * @return $this
     */
    protected function _prepareIndex($entityIds = null, $attributeId = null)
    {
        $connection = $this->getConnection();
        $idxTable = $this->getIdxTable();
        // prepare select attributes
        if ($attributeId === null) {
            $attrIds = $this->_getIndexableAttributes();
        } else {
            $attrIds = [$attributeId];
        }

        if (!$attrIds) {
            return $this;
        }

        $productValueExpression = $connection->getCheckSql('pds.value_id > 0', 'pds.value', 'pdd.value');
        $select = $connection->select()->from(
            ['pdd' => $this->getTable('catalog_product_entity_decimal')],
            ['entity_id', 'attribute_id']
        )->join(
            ['cs' => $this->getTable('store')],
            '',
            ['store_id']
        )->joinLeft(
            ['pds' => $this->getTable('catalog_product_entity_decimal')],
            'pds.entity_id = pdd.entity_id AND pds.attribute_id = pdd.attribute_id' . ' AND pds.store_id=cs.store_id',
            ['value' => $productValueExpression]
        )->where(
            'pdd.store_id=?',
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        )->where(
            'cs.store_id!=?',
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        )->where(
            'pdd.attribute_id IN(?)',
            $attrIds
        )->where(
            "{$productValueExpression} IS NOT NULL"
        );

        $statusCond = $connection->quoteInto(
            '=?',
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        );
        $this->_addAttributeToSelect($select, 'status', 'pdd.entity_id', 'cs.store_id', $statusCond);

        if ($entityIds !== null) {
            $select->where('pdd.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        $this->_eventManager->dispatch(
            'prepare_catalog_product_index_select',
            [
                'select' => $select,
                'entity_field' => new \Zend_Db_Expr('pdd.entity_id'),
                'website_field' => new \Zend_Db_Expr('cs.website_id'),
                'store_field' => new \Zend_Db_Expr('cs.store_id')
            ]
        );

        $query = $select->insertFromSelect($idxTable);
        $connection->query($query);

        return $this;
    }

    /**
     * Retrieve decimal indexable attributes
     *
     * @return array
     */
    protected function _getIndexableAttributes()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['ca' => $this->getTable('catalog_eav_attribute')],
            'attribute_id'
        )->join(
            ['ea' => $this->getTable('eav_attribute')],
            'ca.attribute_id = ea.attribute_id',
            []
        )->where(
            'ea.attribute_code != ?',
            'price'
        )->where(
            $this->_getIndexableAttributesCondition()
        )->where(
            'ea.backend_type=?',
            'decimal'
        );

        return $connection->fetchCol($select);
    }

    /**
     * Retrieve temporary decimal index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        return $this->tableStrategy->getTableName('catalog_product_index_eav_decimal');
    }
}
