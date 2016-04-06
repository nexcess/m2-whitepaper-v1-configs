<?php
namespace Magento\Catalog\Model\Product\Validator;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Product\Validator
 */
class Interceptor extends \Magento\Catalog\Model\Product\Validator implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\Magento\Catalog\Model\Product $product, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\DataObject $response)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        if (!$pluginInfo) {
            return parent::validate($product, $request, $response);
        } else {
            return $this->___callPlugins('validate', func_get_args(), $pluginInfo);
        }
    }
}
