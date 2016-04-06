<?php
namespace Magento\Quote\Model\AddressAdditionalDataProcessor;

/**
 * Interceptor class for @see \Magento\Quote\Model\AddressAdditionalDataProcessor
 */
class Interceptor extends \Magento\Quote\Model\AddressAdditionalDataProcessor implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function process(\Magento\Quote\Api\Data\AddressAdditionalDataInterface $additionalData)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'process');
        if (!$pluginInfo) {
            return parent::process($additionalData);
        } else {
            return $this->___callPlugins('process', func_get_args(), $pluginInfo);
        }
    }
}
