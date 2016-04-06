<?php
namespace Magento\Multishipping\Model\Checkout\Type\Multishipping;

/**
 * Interceptor class for @see
 * \Magento\Multishipping\Model\Checkout\Type\Multishipping
 */
class Interceptor extends \Magento\Multishipping\Model\Checkout\Type\Multishipping implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Checkout\Model\Session $checkoutSession, \Magento\Customer\Model\Session $customerSession, \Magento\Sales\Model\OrderFactory $orderFactory, \Magento\Customer\Api\AddressRepositoryInterface $addressRepository, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Session\Generic $session, \Magento\Quote\Model\Quote\AddressFactory $addressFactory, \Magento\Quote\Model\Quote\Address\ToOrder $quoteAddressToOrder, \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress, \Magento\Quote\Model\Quote\Payment\ToOrderPayment $quotePaymentToOrderPayment, \Magento\Quote\Model\Quote\Item\ToOrderItem $quoteItemToOrderItem, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Payment\Model\Method\SpecificationInterface $paymentSpecification, \Magento\Multishipping\Helper\Data $helper, \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Magento\Framework\Api\FilterBuilder $filterBuilder, \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector, array $data = array())
    {
        $this->___init();
        parent::__construct($checkoutSession, $customerSession, $orderFactory, $addressRepository, $eventManager, $scopeConfig, $session, $addressFactory, $quoteAddressToOrder, $quoteAddressToOrderAddress, $quotePaymentToOrderPayment, $quoteItemToOrderItem, $storeManager, $paymentSpecification, $helper, $orderSender, $priceCurrency, $quoteRepository, $searchCriteriaBuilder, $filterBuilder, $totalsCollector, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteShippingAddressesItems()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQuoteShippingAddressesItems');
        if (!$pluginInfo) {
            return parent::getQuoteShippingAddressesItems();
        } else {
            return $this->___callPlugins('getQuoteShippingAddressesItems', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAddressItem($addressId, $itemId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeAddressItem');
        if (!$pluginInfo) {
            return parent::removeAddressItem($addressId, $itemId);
        } else {
            return $this->___callPlugins('removeAddressItem', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingItemsInformation($info)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setShippingItemsInformation');
        if (!$pluginInfo) {
            return parent::setShippingItemsInformation($info);
        } else {
            return $this->___callPlugins('setShippingItemsInformation', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateQuoteCustomerShippingAddress($addressId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'updateQuoteCustomerShippingAddress');
        if (!$pluginInfo) {
            return parent::updateQuoteCustomerShippingAddress($addressId);
        } else {
            return $this->___callPlugins('updateQuoteCustomerShippingAddress', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteCustomerBillingAddress($addressId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setQuoteCustomerBillingAddress');
        if (!$pluginInfo) {
            return parent::setQuoteCustomerBillingAddress($addressId);
        } else {
            return $this->___callPlugins('setQuoteCustomerBillingAddress', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMethods($methods)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setShippingMethods');
        if (!$pluginInfo) {
            return parent::setShippingMethods($methods);
        } else {
            return $this->___callPlugins('setShippingMethods', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethod($payment)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setPaymentMethod');
        if (!$pluginInfo) {
            return parent::setPaymentMethod($payment);
        } else {
            return $this->___callPlugins('setPaymentMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createOrders()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createOrders');
        if (!$pluginInfo) {
            return parent::createOrders();
        } else {
            return $this->___callPlugins('createOrders', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save();
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'reset');
        if (!$pluginInfo) {
            return parent::reset();
        } else {
            return $this->___callPlugins('reset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateMinimumAmount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validateMinimumAmount');
        if (!$pluginInfo) {
            return parent::validateMinimumAmount();
        } else {
            return $this->___callPlugins('validateMinimumAmount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimumAmountDescription()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMinimumAmountDescription');
        if (!$pluginInfo) {
            return parent::getMinimumAmountDescription();
        } else {
            return $this->___callPlugins('getMinimumAmountDescription', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimumAmountError()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMinimumAmountError');
        if (!$pluginInfo) {
            return parent::getMinimumAmountError();
        } else {
            return $this->___callPlugins('getMinimumAmountError', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderIds($asAssoc = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOrderIds');
        if (!$pluginInfo) {
            return parent::getOrderIds($asAssoc);
        } else {
            return $this->___callPlugins('getOrderIds', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerDefaultBillingAddress()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomerDefaultBillingAddress');
        if (!$pluginInfo) {
            return parent::getCustomerDefaultBillingAddress();
        } else {
            return $this->___callPlugins('getCustomerDefaultBillingAddress', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerDefaultShippingAddress()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomerDefaultShippingAddress');
        if (!$pluginInfo) {
            return parent::getCustomerDefaultShippingAddress();
        } else {
            return $this->___callPlugins('getCustomerDefaultShippingAddress', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutSession()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCheckoutSession');
        if (!$pluginInfo) {
            return parent::getCheckoutSession();
        } else {
            return $this->___callPlugins('getCheckoutSession', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQuote()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQuote');
        if (!$pluginInfo) {
            return parent::getQuote();
        } else {
            return $this->___callPlugins('getQuote', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteItems()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQuoteItems');
        if (!$pluginInfo) {
            return parent::getQuoteItems();
        } else {
            return $this->___callPlugins('getQuoteItems', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerSession()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomerSession');
        if (!$pluginInfo) {
            return parent::getCustomerSession();
        } else {
            return $this->___callPlugins('getCustomerSession', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomer');
        if (!$pluginInfo) {
            return parent::getCustomer();
        } else {
            return $this->___callPlugins('getCustomer', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addData(array $arr)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addData');
        if (!$pluginInfo) {
            return parent::addData($arr);
        } else {
            return $this->___callPlugins('addData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setData');
        if (!$pluginInfo) {
            return parent::setData($key, $value);
        } else {
            return $this->___callPlugins('setData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsetData');
        if (!$pluginInfo) {
            return parent::unsetData($key);
        } else {
            return $this->___callPlugins('unsetData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $index = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getData');
        if (!$pluginInfo) {
            return parent::getData($key, $index);
        } else {
            return $this->___callPlugins('getData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByPath($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByPath');
        if (!$pluginInfo) {
            return parent::getDataByPath($path);
        } else {
            return $this->___callPlugins('getDataByPath', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByKey($key)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByKey');
        if (!$pluginInfo) {
            return parent::getDataByKey($key);
        } else {
            return $this->___callPlugins('getDataByKey', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDataUsingMethod($key, $args = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDataUsingMethod');
        if (!$pluginInfo) {
            return parent::setDataUsingMethod($key, $args);
        } else {
            return $this->___callPlugins('setDataUsingMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataUsingMethod($key, $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataUsingMethod');
        if (!$pluginInfo) {
            return parent::getDataUsingMethod($key, $args);
        } else {
            return $this->___callPlugins('getDataUsingMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'hasData');
        if (!$pluginInfo) {
            return parent::hasData($key);
        } else {
            return $this->___callPlugins('hasData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $keys = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toArray');
        if (!$pluginInfo) {
            return parent::toArray($keys);
        } else {
            return $this->___callPlugins('toArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToArray(array $keys = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToArray');
        if (!$pluginInfo) {
            return parent::convertToArray($keys);
        } else {
            return $this->___callPlugins('convertToArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toXml(array $keys = array(), $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toXml');
        if (!$pluginInfo) {
            return parent::toXml($keys, $rootName, $addOpenTag, $addCdata);
        } else {
            return $this->___callPlugins('toXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToXml(array $arrAttributes = array(), $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToXml');
        if (!$pluginInfo) {
            return parent::convertToXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
        } else {
            return $this->___callPlugins('convertToXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toJson(array $keys = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toJson');
        if (!$pluginInfo) {
            return parent::toJson($keys);
        } else {
            return $this->___callPlugins('toJson', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToJson(array $keys = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToJson');
        if (!$pluginInfo) {
            return parent::convertToJson($keys);
        } else {
            return $this->___callPlugins('convertToJson', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toString($format = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toString');
        if (!$pluginInfo) {
            return parent::toString($format);
        } else {
            return $this->___callPlugins('toString', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $args)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__call');
        if (!$pluginInfo) {
            return parent::__call($method, $args);
        } else {
            return $this->___callPlugins('__call', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isEmpty');
        if (!$pluginInfo) {
            return parent::isEmpty();
        } else {
            return $this->___callPlugins('isEmpty', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($keys = array(), $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'serialize');
        if (!$pluginInfo) {
            return parent::serialize($keys, $valueSeparator, $fieldSeparator, $quote);
        } else {
            return $this->___callPlugins('serialize', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function debug($data = null, &$objects = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'debug');
        if (!$pluginInfo) {
            return parent::debug($data, $objects);
        } else {
            return $this->___callPlugins('debug', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetSet');
        if (!$pluginInfo) {
            return parent::offsetSet($offset, $value);
        } else {
            return $this->___callPlugins('offsetSet', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetExists');
        if (!$pluginInfo) {
            return parent::offsetExists($offset);
        } else {
            return $this->___callPlugins('offsetExists', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetUnset');
        if (!$pluginInfo) {
            return parent::offsetUnset($offset);
        } else {
            return $this->___callPlugins('offsetUnset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetGet');
        if (!$pluginInfo) {
            return parent::offsetGet($offset);
        } else {
            return $this->___callPlugins('offsetGet', func_get_args(), $pluginInfo);
        }
    }
}
