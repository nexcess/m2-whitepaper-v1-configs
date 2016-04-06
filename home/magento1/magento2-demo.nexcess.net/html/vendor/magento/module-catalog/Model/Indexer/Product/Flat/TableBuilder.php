<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat;

use Magento\Framework\App\ResourceConnection;

/**
 * Class TableBuilder
 */
class TableBuilder
{
    /**
     * @var \Magento\Catalog\Helper\Product\Flat\Indexer
     */
    protected $_productIndexerHelper;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * Check whether builder was executed
     *
     * @var bool
     */
    protected $_isExecuted = false;

    /**
     * @param \Magento\Catalog\Helper\Product\Flat\Indexer $productIndexerHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Catalog\Helper\Product\Flat\Indexer $productIndexerHelper,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_productIndexerHelper = $productIndexerHelper;
        $this->_connection = $resource->getConnection();
    }

    /**
     * Prepare temporary tables only for first call of reindex all
     *
     * @param int $storeId
     * @param array $changedIds
     * @param string $valueFieldSuffix
     * @return void
     */
    public function build($storeId, $changedIds, $valueFieldSuffix)
    {
        if ($this->_isExecuted) {
            return;
        }
        $entityTableName = $this->_productIndexerHelper->getTable('catalog_product_entity');
        $attributes = $this->_productIndexerHelper->getAttributes();
        $eavAttributes = $this->_productIndexerHelper->getTablesStructure($attributes);
        $entityTableColumns = $eavAttributes[$entityTableName];

        $temporaryEavAttributes = $eavAttributes;

        //add status global value to the base table
        /* @var $status \Magento\Eav\Model\Entity\Attribute */
        $status = $this->_productIndexerHelper->getAttribute('status');
        $temporaryEavAttributes[$status->getBackendTable()]['status'] = $status;
        //Create list of temporary tables based on available attributes attributes
        $valueTables = [];
        foreach ($temporaryEavAttributes as $tableName => $columns) {
            $valueTables = array_merge(
                $valueTables,
                $this->_createTemporaryTable($this->_getTemporaryTableName($tableName), $columns, $valueFieldSuffix)
            );
        }

        //Fill "base" table which contains all available products
        $this->_fillTemporaryEntityTable($entityTableName, $entityTableColumns, $changedIds);

        //Add primary key to "base" temporary table for increase speed of joins in future
        $this->_addPrimaryKeyToTable($this->_getTemporaryTableName($entityTableName));
        unset($temporaryEavAttributes[$entityTableName]);

        foreach ($temporaryEavAttributes as $tableName => $columns) {
            $temporaryTableName = $this->_getTemporaryTableName($tableName);

            //Add primary key to temporary table for increase speed of joins in future
            $this->_addPrimaryKeyToTable($temporaryTableName);

            //Create temporary table for composite attributes
            if (isset($valueTables[$temporaryTableName . $valueFieldSuffix])) {
                $this->_addPrimaryKeyToTable($temporaryTableName . $valueFieldSuffix);
            }

            //Fill temporary tables with attributes grouped by it type
            $this->_fillTemporaryTable($tableName, $columns, $changedIds, $valueFieldSuffix, $storeId);
        }
        $this->_isExecuted = true;
    }

    /**
     * Create empty temporary table with given columns list
     *
     * @param string $tableName  Table name
     * @param array $columns array('columnName' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute, ...)
     * @param string $valueFieldSuffix
     *
     * @return array
     */
    protected function _createTemporaryTable($tableName, array $columns, $valueFieldSuffix)
    {
        $valueTables = [];
        if (!empty($columns)) {
            $valueTableName = $tableName . $valueFieldSuffix;
            $temporaryTable = $this->_connection->newTable($tableName);
            $valueTemporaryTable = $this->_connection->newTable($valueTableName);
            $flatColumns = $this->_productIndexerHelper->getFlatColumns();

            $temporaryTable->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER);

            $temporaryTable->addColumn('type_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT);

            $temporaryTable->addColumn('attribute_set_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER);

            $valueTemporaryTable->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER);

            /** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            foreach ($columns as $columnName => $attribute) {
                $attributeCode = $attribute->getAttributeCode();
                if (isset($flatColumns[$attributeCode])) {
                    $column = $flatColumns[$attributeCode];
                } else {
                    $column = $attribute->_getFlatColumnsDdlDefinition();
                    $column = $column[$attributeCode];
                }

                $temporaryTable->addColumn(
                    $columnName,
                    $column['type'],
                    isset($column['length']) ? $column['length'] : null
                );

                $columnValueName = $attributeCode . $valueFieldSuffix;
                if (isset($flatColumns[$columnValueName])) {
                    $columnValue = $flatColumns[$columnValueName];
                    $valueTemporaryTable->addColumn(
                        $columnValueName,
                        $columnValue['type'],
                        isset($columnValue['length']) ? $columnValue['length'] : null
                    );
                }
            }
            $this->_connection->dropTemporaryTable($tableName);
            $this->_connection->createTemporaryTable($temporaryTable);

            if (count($valueTemporaryTable->getColumns()) > 1) {
                $this->_connection->dropTemporaryTable($valueTableName);
                $this->_connection->createTemporaryTable($valueTemporaryTable);
                $valueTables[$valueTableName] = $valueTableName;
            }
        }
        return $valueTables;
    }

    /**
     * Retrieve temporary table name by regular table name
     *
     * @param string $tableName
     * @return string
     */
    protected function _getTemporaryTableName($tableName)
    {
        return sprintf('%s_tmp_indexer', $tableName);
    }

    /**
     * Fill temporary entity table
     *
     * @param string $tableName
     * @param array  $columns
     * @param array  $changedIds
     * @return void
     */
    protected function _fillTemporaryEntityTable($tableName, array $columns, array $changedIds = [])
    {
        if (!empty($columns)) {
            $select = $this->_connection->select();
            $temporaryEntityTable = $this->_getTemporaryTableName($tableName);
            $idsColumns = ['entity_id', 'type_id', 'attribute_set_id'];

            $columns = array_merge($idsColumns, array_keys($columns));

            $select->from(['e' => $tableName], $columns);
            $onDuplicate = false;
            if (!empty($changedIds)) {
                $select->where($this->_connection->quoteInto('e.entity_id IN (?)', $changedIds));
                $onDuplicate = true;
            }
            $sql = $select->insertFromSelect($temporaryEntityTable, $columns, $onDuplicate);
            $this->_connection->query($sql);
        }
    }

    /**
     * Add primary key to table by it name
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function _addPrimaryKeyToTable($tableName, $columnName = 'entity_id')
    {
        $this->_connection->addIndex(
            $tableName,
            'entity_id',
            [$columnName],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
        );
    }

    /**
     * Fill temporary table by data from products EAV attributes by type
     *
     * @param string $tableName
     * @param array  $tableColumns
     * @param array  $changedIds
     * @param string $valueFieldSuffix
     * @param int $storeId
     * @return void
     */
    protected function _fillTemporaryTable(
        $tableName,
        array $tableColumns,
        array $changedIds,
        $valueFieldSuffix,
        $storeId
    ) {
        if (!empty($tableColumns)) {
            $columnsChunks = array_chunk(
                $tableColumns,
                Action\Indexer::ATTRIBUTES_CHUNK_SIZE,
                true
            );
            foreach ($columnsChunks as $columnsList) {
                $select = $this->_connection->select();
                $selectValue = $this->_connection->select();
                $entityTableName = $this->_getTemporaryTableName(
                    $this->_productIndexerHelper->getTable('catalog_product_entity')
                );
                $temporaryTableName = $this->_getTemporaryTableName($tableName);
                $temporaryValueTableName = $temporaryTableName . $valueFieldSuffix;
                $keyColumn = ['entity_id'];
                $columns = array_merge($keyColumn, array_keys($columnsList));
                $valueColumns = $keyColumn;
                $flatColumns = $this->_productIndexerHelper->getFlatColumns();
                $iterationNum = 1;

                $select->from(['e' => $entityTableName], $keyColumn);

                $selectValue->from(['e' => $temporaryTableName], $keyColumn);

                /** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
                foreach ($columnsList as $columnName => $attribute) {
                    $countTableName = 't' . $iterationNum++;
                    $joinCondition = sprintf(
                        'e.entity_id = %1$s.entity_id AND %1$s.attribute_id = %2$d AND %1$s.store_id = 0',
                        $countTableName,
                        $attribute->getId()
                    );

                    $select->joinLeft(
                        [$countTableName => $tableName],
                        $joinCondition,
                        [$columnName => 'value']
                    );

                    if ($attribute->getFlatUpdateSelect($storeId) instanceof \Magento\Framework\DB\Select) {
                        $attributeCode = $attribute->getAttributeCode();
                        $columnValueName = $attributeCode . $valueFieldSuffix;
                        if (isset($flatColumns[$columnValueName])) {
                            $valueJoinCondition = sprintf(
                                'e.%1$s = %2$s.option_id AND %2$s.store_id = 0',
                                $attributeCode,
                                $countTableName
                            );
                            $selectValue->joinLeft(
                                [
                                    $countTableName => $this->_productIndexerHelper->getTable(
                                        'eav_attribute_option_value'
                                    ),
                                ],
                                $valueJoinCondition,
                                [$columnValueName => $countTableName . '.value']
                            );
                            $valueColumns[] = $columnValueName;
                        }
                    }
                }

                if (!empty($changedIds)) {
                    $select->where($this->_connection->quoteInto('e.entity_id IN (?)', $changedIds));
                }

                $sql = $select->insertFromSelect($temporaryTableName, $columns, true);
                $this->_connection->query($sql);

                if (count($valueColumns) > 1) {
                    if (!empty($changedIds)) {
                        $selectValue->where($this->_connection->quoteInto('e.entity_id IN (?)', $changedIds));
                    }
                    $sql = $selectValue->insertFromSelect($temporaryValueTableName, $valueColumns, true);
                    $this->_connection->query($sql);
                }
            }
        }
    }
}
