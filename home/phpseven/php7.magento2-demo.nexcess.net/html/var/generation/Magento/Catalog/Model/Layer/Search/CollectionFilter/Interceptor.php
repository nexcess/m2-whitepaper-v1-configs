<?php
namespace Magento\Catalog\Model\Layer\Search\CollectionFilter;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Layer\Search\CollectionFilter
 */
class Interceptor extends \Magento\Catalog\Model\Layer\Search\CollectionFilter implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\Config $catalogConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\Product\Visibility $productVisibility)
    {
        $this->___init();
        parent::__construct($catalogConfig, $storeManager, $productVisibility);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($collection, \Magento\Catalog\Model\Category $category)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'filter');
        if (!$pluginInfo) {
            return parent::filter($collection, $category);
        } else {
            return $this->___callPlugins('filter', func_get_args(), $pluginInfo);
        }
    }
}
