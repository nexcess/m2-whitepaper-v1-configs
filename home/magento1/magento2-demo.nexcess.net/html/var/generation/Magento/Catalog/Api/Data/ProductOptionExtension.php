<?php
namespace Magento\Catalog\Api\Data;

/**
 * Extension class for @see \Magento\Catalog\Api\Data\ProductOptionInterface
 */
class ProductOptionExtension extends \Magento\Framework\Api\AbstractSimpleObject implements \Magento\Catalog\Api\Data\ProductOptionExtensionInterface
{
    /**
     * @return \Magento\Catalog\Api\Data\CustomOptionInterface[]|null
     */
    public function getCustomOptions()
    {
        return $this->_get('custom_options');
    }

    /**
     * @param \Magento\Catalog\Api\Data\CustomOptionInterface[] $customOptions
     * @return $this
     */
    public function setCustomOptions($customOptions)
    {
        $this->setData('custom_options', $customOptions);
        return $this;
    }

    /**
     * @return \Magento\Bundle\Api\Data\BundleOptionInterface[]|null
     */
    public function getBundleOptions()
    {
        return $this->_get('bundle_options');
    }

    /**
     * @param \Magento\Bundle\Api\Data\BundleOptionInterface[] $bundleOptions
     * @return $this
     */
    public function setBundleOptions($bundleOptions)
    {
        $this->setData('bundle_options', $bundleOptions);
        return $this;
    }

    /**
     * @return \Magento\Downloadable\Api\Data\DownloadableOptionInterface|null
     */
    public function getDownloadableOption()
    {
        return $this->_get('downloadable_option');
    }

    /**
     * @param \Magento\Downloadable\Api\Data\DownloadableOptionInterface
     * $downloadableOption
     * @return $this
     */
    public function setDownloadableOption($downloadableOption)
    {
        $this->setData('downloadable_option', $downloadableOption);
        return $this;
    }

    /**
     * @return
     * \Magento\ConfigurableProduct\Api\Data\ConfigurableItemOptionValueInterface[]|null
     */
    public function getConfigurableItemOptions()
    {
        return $this->_get('configurable_item_options');
    }

    /**
     * @param
     * \Magento\ConfigurableProduct\Api\Data\ConfigurableItemOptionValueInterface[]
     * $configurableItemOptions
     * @return $this
     */
    public function setConfigurableItemOptions($configurableItemOptions)
    {
        $this->setData('configurable_item_options', $configurableItemOptions);
        return $this;
    }
}
