<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eav\Model\Entity\Attribute\Source;

class Table extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Default values for option cache
     *
     * @var array
     */
    protected $_optionsDefault = [];

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $_attrOptionCollectionFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory
     */
    protected $_attrOptionFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
    ) {
        $this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->_attrOptionFactory = $attrOptionFactory;
    }

    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Add empty option to array
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $storeId = $this->getAttribute()->getStoreId();
        if (!is_array($this->_options)) {
            $this->_options = [];
        }
        if (!is_array($this->_optionsDefault)) {
            $this->_optionsDefault = [];
        }
        if (!isset($this->_options[$storeId])) {
            $collection = $this->_attrOptionCollectionFactory->create()->setPositionOrder(
                'asc'
            )->setAttributeFilter(
                $this->getAttribute()->getId()
            )->setStoreFilter(
                $this->getAttribute()->getStoreId()
            )->load();
            $this->_options[$storeId] = $collection->toOptionArray();
            $this->_optionsDefault[$storeId] = $collection->toOptionArray('default_value');
        }
        $options = $defaultValues ? $this->_optionsDefault[$storeId] : $this->_options[$storeId];
        if ($withEmpty) {
            array_unshift($options, ['label' => '', 'value' => '']);
        }

        return $options;
    }

    /**
     * Retrieve Option values array by ids
     *
     * @param string|array $ids
     * @param bool $withEmpty Add empty option to array
     * @return array
     */
    public function getSpecificOptions($ids, $withEmpty = true)
    {
        $options = $this->_attrOptionCollectionFactory->create()
            ->setPositionOrder('asc')
            ->setAttributeFilter($this->getAttribute()->getId())
            ->addFieldToFilter('main_table.option_id', ['in' => $ids])
            ->setStoreFilter($this->getAttribute()->getStoreId())
            ->load()
            ->toOptionArray();
        if ($withEmpty) {
            array_unshift($options, ['label' => '', 'value' => '']);
        }
        return $options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return array|string|bool
     */
    public function getOptionText($value)
    {
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        $options = $this->getSpecificOptions($value, false);

        if ($isMultiple) {
            $values = [];
            foreach ($options as $item) {
                if (in_array($item['value'], $value)) {
                    $values[] = $item['label'];
                }
            }
            return $values;
        }

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param string $dir
     *
     * @return $this
     */
    public function addValueSortToCollection($collection, $dir = \Magento\Framework\DB\Select::SQL_ASC)
    {
        $valueTable1 = $this->getAttribute()->getAttributeCode() . '_t1';
        $valueTable2 = $this->getAttribute()->getAttributeCode() . '_t2';
        $collection->getSelect()->joinLeft(
            [$valueTable1 => $this->getAttribute()->getBackend()->getTable()],
            "e.entity_id={$valueTable1}.entity_id" .
            " AND {$valueTable1}.attribute_id='{$this->getAttribute()->getId()}'" .
            " AND {$valueTable1}.store_id=0",
            []
        )->joinLeft(
            [$valueTable2 => $this->getAttribute()->getBackend()->getTable()],
            "e.entity_id={$valueTable2}.entity_id" .
            " AND {$valueTable2}.attribute_id='{$this->getAttribute()->getId()}'" .
            " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
            []
        );
        $valueExpr = $collection->getSelect()->getConnection()->getCheckSql(
            "{$valueTable2}.value_id > 0",
            "{$valueTable2}.value",
            "{$valueTable1}.value"
        );

        $this->_attrOptionFactory->create()->addOptionValueToCollection(
            $collection,
            $this->getAttribute(),
            $valueExpr
        );

        $collection->getSelect()->order("{$this->getAttribute()->getAttributeCode()} {$dir}");

        return $this;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $columns = [];
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $isMulti = $this->getAttribute()->getFrontend()->getInputType() == 'multiselect';

        $type = $isMulti ? \Magento\Framework\DB\Ddl\Table::TYPE_TEXT : \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER;
        $columns[$attributeCode] = [
            'type' => $type,
            'length' => $isMulti ? '255' : null,
            'unsigned' => false,
            'nullable' => true,
            'default' => null,
            'extra' => null,
            'comment' => $attributeCode . ' column',
        ];
        if (!$isMulti) {
            $columns[$attributeCode . '_value'] = [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'unsigned' => false,
                'nullable' => true,
                'default' => null,
                'extra' => null,
                'comment' => $attributeCode . ' column',
            ];
        }

        return $columns;
    }

    /**
     * Retrieve Indexes for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = [];

        $index = sprintf('IDX_%s', strtoupper($this->getAttribute()->getAttributeCode()));
        $indexes[$index] = ['type' => 'index', 'fields' => [$this->getAttribute()->getAttributeCode()]];

        $sortable = $this->getAttribute()->getUsedForSortBy();
        if ($sortable && $this->getAttribute()->getFrontend()->getInputType() != 'multiselect') {
            $index = sprintf('IDX_%s_VALUE', strtoupper($this->getAttribute()->getAttributeCode()));

            $indexes[$index] = [
                'type' => 'index',
                'fields' => [$this->getAttribute()->getAttributeCode() . '_value'],
            ];
        }

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->_attrOptionFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
