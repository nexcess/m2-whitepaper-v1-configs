<?php
namespace Magento\Catalog\Model\Product\TypeTransitionManager;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Product\TypeTransitionManager
 */
class Interceptor extends \Magento\Catalog\Model\Product\TypeTransitionManager implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\Product\Edit\WeightResolver $weightResolver, array $compatibleTypes)
    {
        $this->___init();
        parent::__construct($weightResolver, $compatibleTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function processProduct(\Magento\Catalog\Model\Product $product)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'processProduct');
        if (!$pluginInfo) {
            return parent::processProduct($product);
        } else {
            return $this->___callPlugins('processProduct', func_get_args(), $pluginInfo);
        }
    }
}
