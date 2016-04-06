<?php
namespace Magento\Sales\Model\Order\Admin\Item;

/**
 * Interceptor class for @see \Magento\Sales\Model\Order\Admin\Item
 */
class Interceptor extends \Magento\Sales\Model\Order\Admin\Item implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function getSku(\Magento\Sales\Model\Order\Item $item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSku');
        if (!$pluginInfo) {
            return parent::getSku($item);
        } else {
            return $this->___callPlugins('getSku', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(\Magento\Sales\Model\Order\Item $item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getName');
        if (!$pluginInfo) {
            return parent::getName($item);
        } else {
            return $this->___callPlugins('getName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId(\Magento\Sales\Model\Order\Item $item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProductId');
        if (!$pluginInfo) {
            return parent::getProductId($item);
        } else {
            return $this->___callPlugins('getProductId', func_get_args(), $pluginInfo);
        }
    }
}
