<?php
namespace Magento\Catalog\Model\Entity\Product\Attribute\Group\AttributeMapper;

/**
 * Interceptor class for @see
 * \Magento\Catalog\Model\Entity\Product\Attribute\Group\AttributeMapper
 */
class Interceptor extends \Magento\Catalog\Model\Entity\Product\Attribute\Group\AttributeMapper implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\Attribute\Config $attributeConfig)
    {
        $this->___init();
        parent::__construct($attributeConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function map(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'map');
        if (!$pluginInfo) {
            return parent::map($attribute);
        } else {
            return $this->___callPlugins('map', func_get_args(), $pluginInfo);
        }
    }
}
