<?php
namespace Magento\Catalog\Model\Product\CartConfiguration;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Product\CartConfiguration
 */
class Interceptor extends \Magento\Catalog\Model\Product\CartConfiguration implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function isProductConfigured(\Magento\Catalog\Model\Product $product, $config)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isProductConfigured');
        if (!$pluginInfo) {
            return parent::isProductConfigured($product, $config);
        } else {
            return $this->___callPlugins('isProductConfigured', func_get_args(), $pluginInfo);
        }
    }
}
