<?php
namespace Magento\Paypal\Controller\Express\Start;

/**
 * Interceptor class for @see \Magento\Paypal\Controller\Express\Start
 */
class Interceptor extends \Magento\Paypal\Controller\Express\Start implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Sales\Model\OrderFactory $orderFactory, \Magento\Paypal\Model\Express\Checkout\Factory $checkoutFactory, \Magento\Framework\Session\Generic $paypalSession, \Magento\Framework\Url\Helper\Data $urlHelper, \Magento\Customer\Model\Url $customerUrl)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $checkoutSession, $orderFactory, $checkoutFactory, $paypalSession, $urlHelper, $customerUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute();
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerBeforeAuthUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCustomerBeforeAuthUrl');
        if (!$pluginInfo) {
            return parent::getCustomerBeforeAuthUrl();
        } else {
            return $this->___callPlugins('getCustomerBeforeAuthUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlagList()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlagList');
        if (!$pluginInfo) {
            return parent::getActionFlagList();
        } else {
            return $this->___callPlugins('getActionFlagList', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLoginUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLoginUrl');
        if (!$pluginInfo) {
            return parent::getLoginUrl();
        } else {
            return $this->___callPlugins('getLoginUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectActionName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRedirectActionName');
        if (!$pluginInfo) {
            return parent::getRedirectActionName();
        } else {
            return $this->___callPlugins('getRedirectActionName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function redirectLogin()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'redirectLogin');
        if (!$pluginInfo) {
            return parent::redirectLogin();
        } else {
            return $this->___callPlugins('redirectLogin', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        if (!$pluginInfo) {
            return parent::getActionFlag();
        } else {
            return $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        if (!$pluginInfo) {
            return parent::getRequest();
        } else {
            return $this->___callPlugins('getRequest', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        if (!$pluginInfo) {
            return parent::getResponse();
        } else {
            return $this->___callPlugins('getResponse', func_get_args(), $pluginInfo);
        }
    }
}
