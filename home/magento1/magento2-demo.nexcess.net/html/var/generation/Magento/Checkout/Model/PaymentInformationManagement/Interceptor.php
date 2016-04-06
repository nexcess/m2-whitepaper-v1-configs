<?php
namespace Magento\Checkout\Model\PaymentInformationManagement;

/**
 * Interceptor class for @see \Magento\Checkout\Model\PaymentInformationManagement
 */
class Interceptor extends \Magento\Checkout\Model\PaymentInformationManagement implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Quote\Api\BillingAddressManagementInterface $billingAddressManagement, \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement, \Magento\Quote\Api\CartManagementInterface $cartManagement, \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory, \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository)
    {
        $this->___init();
        parent::__construct($billingAddressManagement, $paymentMethodManagement, $cartManagement, $paymentDetailsFactory, $cartTotalsRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function savePaymentInformationAndPlaceOrder($cartId, \Magento\Quote\Api\Data\PaymentInterface $paymentMethod, \Magento\Quote\Api\Data\AddressInterface $billingAddress = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'savePaymentInformationAndPlaceOrder');
        if (!$pluginInfo) {
            return parent::savePaymentInformationAndPlaceOrder($cartId, $paymentMethod, $billingAddress);
        } else {
            return $this->___callPlugins('savePaymentInformationAndPlaceOrder', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function savePaymentInformation($cartId, \Magento\Quote\Api\Data\PaymentInterface $paymentMethod, \Magento\Quote\Api\Data\AddressInterface $billingAddress = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'savePaymentInformation');
        if (!$pluginInfo) {
            return parent::savePaymentInformation($cartId, $paymentMethod, $billingAddress);
        } else {
            return $this->___callPlugins('savePaymentInformation', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentInformation($cartId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPaymentInformation');
        if (!$pluginInfo) {
            return parent::getPaymentInformation($cartId);
        } else {
            return $this->___callPlugins('getPaymentInformation', func_get_args(), $pluginInfo);
        }
    }
}
