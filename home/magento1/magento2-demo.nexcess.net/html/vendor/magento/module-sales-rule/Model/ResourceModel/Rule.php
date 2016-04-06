<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRule\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * Sales Rule resource model
 */
class Rule extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'website' => [
            'associations_table' => 'salesrule_website',
            'rule_id_field' => 'rule_id',
            'entity_id_field' => 'website_id',
        ],
        'customer_group' => [
            'associations_table' => 'salesrule_customer_group',
            'rule_id_field' => 'rule_id',
            'entity_id_field' => 'customer_group_id',
        ],
    ];

    /**
     * @var array
     */
    protected $customerGroupIds = [];

    /**
     * @var array
     */
    protected $websiteIds = [];

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon
     */
    protected $_resourceCoupon;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon $resourceCoupon
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\SalesRule\Model\ResourceModel\Coupon $resourceCoupon,
        $connectionName = null
    ) {
        $this->string = $string;
        $this->_resourceCoupon = $resourceCoupon;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('salesrule', 'rule_id');
    }

    /**
     * Add customer group ids and website ids to rule data after load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadCustomerGroupIds($object);
        $this->loadWebsiteIds($object);

        parent::_afterLoad($object);
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    public function loadCustomerGroupIds(AbstractModel $object)
    {
        if (!$this->customerGroupIds) {
            $this->customerGroupIds = (array)$this->getCustomerGroupIds($object->getId());
        }
        $object->setData('customer_group_ids', $this->customerGroupIds);
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    public function loadWebsiteIds(AbstractModel $object)
    {
        if (!$this->websiteIds) {
            $this->websiteIds = (array)$this->getWebsiteIds($object->getId());
        }

        $object->setData('website_ids', $this->websiteIds);
    }

    /**
     * Prepare sales rule's discount quantity
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(AbstractModel $object)
    {
        if (!$object->getDiscountQty()) {
            $object->setDiscountQty(new \Zend_Db_Expr('NULL'));
        }

        parent::_beforeSave($object);
        return $this;
    }

    /**
     * Bind sales rule to customer group(s) and website(s).
     * Save rule's associated store labels.
     * Save product attributes used in rule.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->hasStoreLabels()) {
            $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        }

        if ($object->hasWebsiteIds()) {
            $websiteIds = $object->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($object->getId(), $websiteIds, 'website');
        }

        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->bindRuleToEntity($object->getId(), $customerGroupIds, 'customer_group');
        }

        // Save product attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->getProductAttributes(serialize($object->getConditions()->asArray())),
            $this->getProductAttributes(serialize($object->getActions()->asArray()))
        );
        if (count($ruleProductAttributes)) {
            $this->setActualProductAttributes($object, $ruleProductAttributes);
        }

        // Update auto geterated specific coupons if exists
        if ($object->getUseAutoGeneration() && $object->hasDataChanges()) {
            $this->_resourceCoupon->updateSpecificCoupons($object);
        }
        return parent::_afterSave($object);
    }

    /**
     * Retrieve coupon/rule uses for specified customer
     *
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param int $customerId
     * @return string
     */
    public function getCustomerUses($rule, $customerId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('rule_customer'),
            ['cnt' => 'count(*)']
        )->where(
            'rule_id = :rule_id'
        )->where(
            'customer_id = :customer_id'
        );
        return $connection->fetchOne($select, [':rule_id' => $rule->getRuleId(), ':customer_id' => $customerId]);
    }

    /**
     * Save rule labels for different store views
     *
     * @param int $ruleId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($ruleId, $labels)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable('salesrule_label');
        $connection = $this->getConnection();

        $data = [];
        foreach ($labels as $storeId => $label) {
            if ($this->string->strlen($label)) {
                $data[] = ['rule_id' => $ruleId, 'store_id' => $storeId, 'label' => $label];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $connection->insertOnDuplicate($table, $data, ['label']);
            }

            if (!empty($deleteByStoreIds)) {
                $connection->delete($table, ['rule_id=?' => $ruleId, 'store_id IN (?)' => $deleteByStoreIds]);
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing rule labels
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreLabels($ruleId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('salesrule_label'),
            ['store_id', 'label']
        )->where(
            'rule_id = :rule_id'
        );
        return $this->getConnection()->fetchPairs($select, [':rule_id' => $ruleId]);
    }

    /**
     * Get rule label by specific store id
     *
     * @param int $ruleId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($ruleId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('salesrule_label'),
            'label'
        )->where(
            'rule_id = :rule_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );
        return $this->getConnection()->fetchOne($select, [':rule_id' => $ruleId, ':store_id' => $storeId]);
    }

    /**
     * Return codes of all product attributes currently used in promo rules
     *
     * @return array
     */
    public function getActiveAttributes()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['a' => $this->getTable('salesrule_product_attribute')],
            new \Zend_Db_Expr('DISTINCT ea.attribute_code')
        )->joinInner(
            ['ea' => $this->getTable('eav_attribute')],
            'ea.attribute_id = a.attribute_id',
            []
        );
        return $connection->fetchAll($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of rule
     *
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param mixed $attributes
     * @return $this
     */
    public function setActualProductAttributes($rule, $attributes)
    {
        $connection = $this->getConnection();
        $connection->delete($this->getTable('salesrule_product_attribute'), ['rule_id=?' => $rule->getId()]);

        //Getting attribute IDs for attribute codes
        $attributeIds = [];
        $select = $this->getConnection()->select()->from(
            ['a' => $this->getTable('eav_attribute')],
            ['a.attribute_id']
        )->where(
            'a.attribute_code IN (?)',
            [$attributes]
        );
        $attributesFound = $this->getConnection()->fetchAll($select);
        if ($attributesFound) {
            foreach ($attributesFound as $attribute) {
                $attributeIds[] = $attribute['attribute_id'];
            }

            $data = [];
            foreach ($rule->getCustomerGroupIds() as $customerGroupId) {
                foreach ($rule->getWebsiteIds() as $websiteId) {
                    foreach ($attributeIds as $attribute) {
                        $data[] = [
                            'rule_id' => $rule->getId(),
                            'website_id' => $websiteId,
                            'customer_group_id' => $customerGroupId,
                            'attribute_id' => $attribute,
                        ];
                    }
                }
            }
            $connection->insertMultiple($this->getTable('salesrule_product_attribute'), $data);
        }

        return $this;
    }

    /**
     * Collect all product attributes used in serialized rule's action or condition
     *
     * @param string $serializedString
     * @return array
     */
    public function getProductAttributes($serializedString)
    {
        $result = [];
        if (preg_match_all(
            '~s:32:"salesrule/rule_condition_product";s:9:"attribute";s:\d+:"(.*?)"~s',
            $serializedString,
            $matches
        )
        ) {
            foreach ($matches[1] as $attributeCode) {
                $result[] = $attributeCode;
            }
        }

        return $result;
    }
}
