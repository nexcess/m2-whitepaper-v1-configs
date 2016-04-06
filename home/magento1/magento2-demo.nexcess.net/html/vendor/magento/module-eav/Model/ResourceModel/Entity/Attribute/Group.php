<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eav\Model\ResourceModel\Entity\Attribute;

/**
 * Eav Resource Entity Attribute Group
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Group extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Constants for attribute group codes
     */
    const TAB_GENERAL_CODE = 'product-details';

    const TAB_IMAGE_MANAGEMENT_CODE = 'image-management';

    /**
     * Resource initialization
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init('eav_attribute_group', 'attribute_group_id');
    }

    /**
     * Checks if attribute group exists
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Group $object
     * @return bool
     */
    public function itemExists($object)
    {
        $connection = $this->getConnection();
        $bind = [
            'attribute_set_id' => $object->getAttributeSetId(),
            'attribute_group_name' => $object->getAttributeGroupName(),
        ];
        $select = $connection->select()->from(
            $this->getMainTable()
        )->where(
            'attribute_set_id = :attribute_set_id'
        )->where(
            'attribute_group_name = :attribute_group_name'
        );

        return $connection->fetchRow($select, $bind) > 0;
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getSortOrder()) {
            $object->setSortOrder($this->_getMaxSortOrder($object) + 1);
        }
        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getAttributes()) {
            foreach ($object->getAttributes() as $attribute) {
                $attribute->setAttributeGroupId($object->getId());
                $attribute->save();
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve max sort order
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    protected function _getMaxSortOrder($object)
    {
        $connection = $this->getConnection();
        $bind = [':attribute_set_id' => $object->getAttributeSetId()];
        $select = $connection->select()->from(
            $this->getMainTable(),
            new \Zend_Db_Expr("MAX(sort_order)")
        )->where(
            'attribute_set_id = :attribute_set_id'
        );

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Set any group default if old one was removed
     *
     * @param integer $attributeSetId
     * @return $this
     */
    public function updateDefaultGroup($attributeSetId)
    {
        $connection = $this->getConnection();
        $bind = [':attribute_set_id' => $attributeSetId];
        $select = $connection->select()->from(
            $this->getMainTable(),
            $this->getIdFieldName()
        )->where(
            'attribute_set_id = :attribute_set_id'
        )->order(
            'default_id ' . \Magento\Framework\Data\Collection::SORT_ORDER_DESC
        )->limit(
            1
        );

        $groupId = $connection->fetchOne($select, $bind);

        if ($groupId) {
            $data = ['default_id' => 1];
            $where = ['attribute_group_id =?' => $groupId];
            $connection->update($this->getMainTable(), $data, $where);
        }

        return $this;
    }
}
