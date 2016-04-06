<?php
/**
 * Configurable product type resource model
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableProduct\Model\ResourceModel\Product\Type;

class Configurable extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Catalog product relation
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Relation
     */
    protected $_catalogProductRelation;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\Relation $catalogProductRelation
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\Relation $catalogProductRelation,
        $connectionName = null
    ) {
        $this->_catalogProductRelation = $catalogProductRelation;
        parent::__construct($context, $connectionName);
    }

    /**
     * Init resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_super_link', 'link_id');
    }

    /**
     * Save configurable product relations
     *
     * @param \Magento\Catalog\Model\Product $mainProduct the parent id
     * @param array $productIds the children id array
     * @return $this
     */
    public function saveProducts($mainProduct, $productIds)
    {
        $isProductInstance = false;
        if ($mainProduct instanceof \Magento\Catalog\Model\Product) {
            $mainProductId = $mainProduct->getId();
            $isProductInstance = true;
        }
        $old = [];
        if (!$mainProduct->getIsDuplicate()) {
            $old = $mainProduct->getTypeInstance()->getUsedProductIds($mainProduct);
        }

        $insert = array_diff($productIds, $old);
        $delete = array_diff($old, $productIds);

        if ((!empty($insert) || !empty($delete)) && $isProductInstance) {
            $mainProduct->setIsRelationsChanged(true);
        }

        if (!empty($delete)) {
            $where = ['parent_id = ?' => $mainProductId, 'product_id IN(?)' => $delete];
            $this->getConnection()->delete($this->getMainTable(), $where);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $childId) {
                $data[] = ['product_id' => (int)$childId, 'parent_id' => (int)$mainProductId];
            }
            $this->getConnection()->insertMultiple($this->getMainTable(), $data);
        }

        // configurable product relations should be added to relation table
        $this->_catalogProductRelation->processRelations($mainProductId, $productIds);

        return $this;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int|array $parentId
     * @param bool $required
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getChildrenIds($parentId, $required = true)
    {
        $select = $this->getConnection()->select()->from(
            ['l' => $this->getMainTable()],
            ['product_id', 'parent_id']
        )->join(
            ['e' => $this->getTable('catalog_product_entity')],
            'e.entity_id = l.product_id AND e.required_options = 0',
            []
        )->where(
            'parent_id IN (?)',
            $parentId
        );

        $childrenIds = [0 => []];
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $childrenIds[0][$row['product_id']] = $row['product_id'];
        }

        return $childrenIds;
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param int|array $childId
     * @return string[]
     */
    public function getParentIdsByChild($childId)
    {
        $parentIds = [];

        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            ['product_id', 'parent_id']
        )->where(
            'product_id IN(?)',
            $childId
        );
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $parentIds[] = $row['parent_id'];
        }

        return $parentIds;
    }

    /**
     * Collect product options with values according to the product instance and attributes, that were received
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $attributes
     * @return array
     */
    public function getConfigurableOptions($product, $attributes)
    {
        $attributesOptionsData = [];
        foreach ($attributes as $superAttribute) {
            $select = $this->getConnection()->select()->from(
                ['super_attribute' => $this->getTable('catalog_product_super_attribute')],
                [
                    'sku' => 'entity.sku',
                    'product_id' => 'super_attribute.product_id',
                    'attribute_code' => 'attribute.attribute_code',
                    'option_title' => 'option_value.value',
                    'super_attribute_label' => 'attribute_label.value',
                ]
            )->joinInner(
                ['product_link' => $this->getTable('catalog_product_super_link')],
                'product_link.parent_id = super_attribute.product_id',
                []
            )->joinInner(
                ['attribute' => $this->getTable('eav_attribute')],
                'attribute.attribute_id = super_attribute.attribute_id',
                []
            )->joinInner(
                ['entity' => $this->getTable('catalog_product_entity')],
                'entity.entity_id = product_link.product_id',
                []
            )->joinInner(
                ['entity_value' => $superAttribute->getBackendTable()],
                implode(
                    ' AND ',
                    [
                        'entity_value.attribute_id = super_attribute.attribute_id',
                        'entity_value.store_id = 0',
                        'entity_value.entity_id = product_link.product_id'
                    ]
                ),
                []
            )->joinLeft(
                ['option_value' => $this->getTable('eav_attribute_option_value')],
                implode(
                    ' AND ',
                    [
                        'option_value.option_id = entity_value.value',
                        'option_value.store_id = ' . \Magento\Store\Model\Store::DEFAULT_STORE_ID
                    ]
                ),
                []
            )->joinLeft(
                ['attribute_label' => $this->getTable('catalog_product_super_attribute_label')],
                implode(
                    ' AND ',
                    [
                        'super_attribute.product_super_attribute_id = attribute_label.product_super_attribute_id',
                        'attribute_label.store_id = ' . \Magento\Store\Model\Store::DEFAULT_STORE_ID
                    ]
                ),
                []
            )->where(
                'super_attribute.product_id = ?',
                $product->getId()
            );

            $attributesOptionsData[$superAttribute->getAttributeId()] = $this->getConnection()->fetchAll($select);
        }
        return $attributesOptionsData;
    }
}
