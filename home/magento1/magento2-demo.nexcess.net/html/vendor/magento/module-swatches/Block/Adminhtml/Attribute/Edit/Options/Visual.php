<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options;

/**
 * Block Class for Visual Swatch
 */
class Visual extends AbstractSwatch
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Swatches::catalog/product/attribute/visual.phtml';

    /**
     * Create store values
     *
     * @codeCoverageIgnore
     * @param integer $storeId
     * @param integer $optionId
     * @return array
     */
    protected function createStoreValues($storeId, $optionId)
    {
        $value = [];
        $value['store' . $storeId] = '';
        $value['defaultswatch' . $storeId] = '';
        $value['swatch' . $storeId] = '';
        $storeValues = $this->getStoreOptionValues($storeId);
        $swatchStoreValue = null;

        if (isset($storeValues['swatch'])) {
            $swatchStoreValue = $storeValues['swatch'];
        }

        if (isset($storeValues[$optionId])) {
            $value['store' . $storeId] = $this->escapeHtml($storeValues[$optionId]);
        }

        if (isset($swatchStoreValue[$optionId])) {
            $value['defaultswatch' . $storeId] = $this->escapeHtml($swatchStoreValue[$optionId]);
        }

        $swatchStoreValue = $this->reformatSwatchLabels($swatchStoreValue);
        if (isset($swatchStoreValue[$optionId])) {
            $value['swatch' . $storeId] = $this->escapeHtml($swatchStoreValue[$optionId]);
        }

        return $value;
    }

    /**
     * Parse swatch labels for template
     *
     * @codeCoverageIgnore
     * @param null $swatchStoreValue
     * @return string
     */
    protected function reformatSwatchLabels($swatchStoreValue = null)
    {
        if ($swatchStoreValue === null) {
            return;
        }
        $newSwatch = '';
        foreach ($swatchStoreValue as $key => $value) {
            if ($value[0] == '#') {
                $newSwatch[$key] = 'background: '.$value;
            } elseif ($value[0] == '/') {
                $mediaUrl = $this->swatchHelper->getSwatchMediaUrl();
                $newSwatch[$key] = 'background: url('.$mediaUrl.$value.'); background-size: cover;';
            }
        }
        return $newSwatch;
    }
}
