<?php
namespace Magento\Tax\Model\Calculation;

/**
 * Proxy class for @see \Magento\Tax\Model\Calculation
 */
class Proxy extends \Magento\Tax\Model\Calculation implements \Magento\Framework\ObjectManager\NoninterceptableInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\Tax\\Model\\Calculation', $shared = true)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array('_subject', '_isShared');
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\Tax\Model\Calculation
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCustomerTaxClass($store = null)
    {
        return $this->_getSubject()->getDefaultCustomerTaxClass($store);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByRuleId($ruleId)
    {
        return $this->_getSubject()->deleteByRuleId($ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRates($ruleId)
    {
        return $this->_getSubject()->getRates($ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerTaxClasses($ruleId)
    {
        return $this->_getSubject()->getCustomerTaxClasses($ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTaxClasses($ruleId)
    {
        return $this->_getSubject()->getProductTaxClasses($ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRate($request)
    {
        return $this->_getSubject()->getRate($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreRate($request, $store = null)
    {
        return $this->_getSubject()->getStoreRate($request, $store);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultRateRequest($store = null, $customerId = null)
    {
        return $this->_getSubject()->getDefaultRateRequest($store, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRateRequest($shippingAddress = null, $billingAddress = null, $customerTaxClass = null, $store = null, $customerId = null)
    {
        return $this->_getSubject()->getRateRequest($shippingAddress, $billingAddress, $customerTaxClass, $store, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedRates($request)
    {
        return $this->_getSubject()->getAppliedRates($request);
    }

    /**
     * {@inheritdoc}
     */
    public function reproduceProcess($rates)
    {
        return $this->_getSubject()->reproduceProcess($rates);
    }

    /**
     * {@inheritdoc}
     */
    public function calcTaxAmount($price, $taxRate, $priceIncludeTax = false, $round = true)
    {
        return $this->_getSubject()->calcTaxAmount($price, $taxRate, $priceIncludeTax, $round);
    }

    /**
     * {@inheritdoc}
     */
    public function round($price)
    {
        return $this->_getSubject()->round($price);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRates($billingAddress, $shippingAddress, $customerTaxClassId)
    {
        return $this->_getSubject()->getTaxRates($billingAddress, $shippingAddress, $customerTaxClassId);
    }

    /**
     * {@inheritdoc}
     */
    public function setIdFieldName($name)
    {
        return $this->_getSubject()->setIdFieldName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdFieldName()
    {
        return $this->_getSubject()->getIdFieldName();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_getSubject()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($value)
    {
        return $this->_getSubject()->setId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted($isDeleted = null)
    {
        return $this->_getSubject()->isDeleted($isDeleted);
    }

    /**
     * {@inheritdoc}
     */
    public function hasDataChanges()
    {
        return $this->_getSubject()->hasDataChanges();
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        return $this->_getSubject()->setData($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key = null)
    {
        return $this->_getSubject()->unsetData($key);
    }

    /**
     * {@inheritdoc}
     */
    public function setDataChanges($value)
    {
        return $this->_getSubject()->setDataChanges($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrigData($key = null)
    {
        return $this->_getSubject()->getOrigData($key);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrigData($key = null, $data = null)
    {
        return $this->_getSubject()->setOrigData($key, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function dataHasChangedFor($field)
    {
        return $this->_getSubject()->dataHasChangedFor($field);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceName()
    {
        return $this->_getSubject()->getResourceName();
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollection()
    {
        return $this->_getSubject()->getResourceCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->_getSubject()->getCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function load($modelId, $field = null)
    {
        return $this->_getSubject()->load($modelId, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function afterLoad()
    {
        return $this->_getSubject()->afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    public function isSaveAllowed()
    {
        return $this->_getSubject()->isSaveAllowed();
    }

    /**
     * {@inheritdoc}
     */
    public function setHasDataChanges($flag)
    {
        return $this->_getSubject()->setHasDataChanges($flag);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        return $this->_getSubject()->save();
    }

    /**
     * {@inheritdoc}
     */
    public function afterCommitCallback()
    {
        return $this->_getSubject()->afterCommitCallback();
    }

    /**
     * {@inheritdoc}
     */
    public function isObjectNew($flag = null)
    {
        return $this->_getSubject()->isObjectNew($flag);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        return $this->_getSubject()->beforeSave();
    }

    /**
     * {@inheritdoc}
     */
    public function validateBeforeSave()
    {
        return $this->_getSubject()->validateBeforeSave();
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTags()
    {
        return $this->_getSubject()->getCacheTags();
    }

    /**
     * {@inheritdoc}
     */
    public function cleanModelCache()
    {
        return $this->_getSubject()->cleanModelCache();
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        return $this->_getSubject()->afterSave();
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        return $this->_getSubject()->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        return $this->_getSubject()->beforeDelete();
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        return $this->_getSubject()->afterDelete();
    }

    /**
     * {@inheritdoc}
     */
    public function afterDeleteCommit()
    {
        return $this->_getSubject()->afterDeleteCommit();
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return $this->_getSubject()->getResource();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->_getSubject()->getEntityId();
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        return $this->_getSubject()->setEntityId($entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function clearInstance()
    {
        return $this->_getSubject()->clearInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function getStoredData()
    {
        return $this->_getSubject()->getStoredData();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventPrefix()
    {
        return $this->_getSubject()->getEventPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function addData(array $arr)
    {
        return $this->_getSubject()->addData($arr);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $index = null)
    {
        return $this->_getSubject()->getData($key, $index);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByPath($path)
    {
        return $this->_getSubject()->getDataByPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByKey($key)
    {
        return $this->_getSubject()->getDataByKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function setDataUsingMethod($key, $args = array())
    {
        return $this->_getSubject()->setDataUsingMethod($key, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataUsingMethod($key, $args = null)
    {
        return $this->_getSubject()->getDataUsingMethod($key, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key = '')
    {
        return $this->_getSubject()->hasData($key);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $keys = array())
    {
        return $this->_getSubject()->toArray($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToArray(array $keys = array())
    {
        return $this->_getSubject()->convertToArray($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function toXml(array $keys = array(), $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        return $this->_getSubject()->toXml($keys, $rootName, $addOpenTag, $addCdata);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToXml(array $arrAttributes = array(), $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        return $this->_getSubject()->convertToXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
    }

    /**
     * {@inheritdoc}
     */
    public function toJson(array $keys = array())
    {
        return $this->_getSubject()->toJson($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToJson(array $keys = array())
    {
        return $this->_getSubject()->convertToJson($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function toString($format = '')
    {
        return $this->_getSubject()->toString($format);
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $args)
    {
        return $this->_getSubject()->__call($method, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->_getSubject()->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($keys = array(), $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"')
    {
        return $this->_getSubject()->serialize($keys, $valueSeparator, $fieldSeparator, $quote);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($data = null, &$objects = array())
    {
        return $this->_getSubject()->debug($data, $objects);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        return $this->_getSubject()->offsetSet($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->_getSubject()->offsetExists($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        return $this->_getSubject()->offsetUnset($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->_getSubject()->offsetGet($offset);
    }
}
