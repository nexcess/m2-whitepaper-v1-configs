<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableProduct\Test\Handler\ConfigurableProduct;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as ProductCurl;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProduct\ConfigurableAttributesData;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Class Curl
 * Create new configurable product via curl
 */
class Curl extends ProductCurl implements ConfigurableProductInterface
{
    /**
     * Constructor
     *
     * @param DataInterface $configuration
     * @param EventManagerInterface $eventManager
     */
    public function __construct(DataInterface $configuration, EventManagerInterface $eventManager)
    {
        parent::__construct($configuration, $eventManager);

        $this->mappingData += [
            'is_percent' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'include' => [
                'Yes' => 1,
                'No' => 0,
            ]
        ];
    }

    /**
     * Prepare POST data for creating product request
     *
     * @param FixtureInterface $product
     * @param string|null $prefix [optional]
     * @return array
     */
    protected function prepareData(FixtureInterface $product, $prefix = null)
    {
        $data = parent::prepareData($product, null);

        /** @var ConfigurableAttributesData $configurableAttributesData */
        $configurableAttributesData = $product->getDataFieldConfig('configurable_attributes_data')['source'];
        $attributeSetId = $data['attribute_set_id'];

        $data['configurable_attributes_data'] = $this->prepareAttributesData($configurableAttributesData);
        $data = $prefix ? [$prefix => $data] : $data;
        $data['variations-matrix'] = $this->prepareVariationsMatrix($product);
        $data['attributes'] = $this->prepareAttributes($configurableAttributesData);
        $data['new-variations-attribute-set-id'] = $attributeSetId;
        $data['associated_product_ids'] = $this->prepareAssociatedProductIds($configurableAttributesData);

        return $this->replaceMappingData($data);
    }

    /**
     * Preparing attribute data
     *
     * @param ConfigurableAttributesData $configurableAttributesData
     * @return array
     */
    protected function prepareAttributesData(ConfigurableAttributesData $configurableAttributesData)
    {
        $optionFields = [
            'pricing_value',
            'is_percent',
            'include',
        ];
        $result = [];

        foreach ($configurableAttributesData->getAttributesData() as $attribute) {
            $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : null;
            $dataOptions = [];

            foreach ($attribute['options'] as $option) {
                $optionId = isset($option['id']) ? $option['id'] : null;

                $dataOption = array_intersect_key($option, array_flip($optionFields));
                $dataOption['value_index'] = $optionId;

                $dataOptions[$optionId] = $dataOption;
            }

            $result[$attributeId] = [
                'code' => $attribute['attribute_code'],
                'attribute_id' => $attributeId,
                'label' => $attribute['frontend_label'],
                'values' => $dataOptions,
            ];
        }

        return $result;
    }

    /**
     * Preparing matrix data
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareVariationsMatrix(FixtureInterface $product)
    {
        /** @var ConfigurableAttributesData $configurableAttributesData */
        $configurableAttributesData = $product->getDataFieldConfig('configurable_attributes_data')['source'];
        $attributesData = $configurableAttributesData->getAttributesData();
        $assignedProducts = $configurableAttributesData->getProducts();
        $matrixData = $product->getConfigurableAttributesData()['matrix'];
        $result = [];

        foreach ($matrixData as $variationKey => $variation) {
            // For assigned products doesn't send data about them
            if (isset($assignedProducts[$variationKey])) {
                continue;
            }

            $compositeKeys = explode(' ', $variationKey);
            $keyIds = [];
            $configurableAttribute = [];

            foreach ($compositeKeys as $compositeKey) {
                list($attributeKey, $optionKey) = explode(':', $compositeKey);
                $attribute = $attributesData[$attributeKey];

                $keyIds[] = $attribute['options'][$optionKey]['id'];
                $configurableAttribute[] = sprintf(
                    '"%s":"%s"',
                    $attribute['attribute_code'],
                    $attribute['options'][$optionKey]['id']
                );
            }

            $keyIds = implode('-', $keyIds);
            $variation['configurable_attribute'] = '{' . implode(',', $configurableAttribute) . '}';
            $result[$keyIds] = $variation;
        }

        return $result;
    }

    /**
     * Prepare attributes
     *
     * @param ConfigurableAttributesData $configurableAttributesData
     * @return array
     */
    protected function prepareAttributes(ConfigurableAttributesData $configurableAttributesData)
    {
        $ids = [];

        foreach ($configurableAttributesData->getAttributes() as $attribute) {
            /** @var CatalogProductAttribute $attribute */
            $ids[] = $attribute->getAttributeId();
        }
        return $ids;
    }

    /**
     * Prepare associated product ids
     *
     * @param ConfigurableAttributesData $configurableAttributesData
     * @return array
     */
    protected function prepareAssociatedProductIds(ConfigurableAttributesData $configurableAttributesData)
    {
        $productIds = [];

        foreach ($configurableAttributesData->getProducts() as $product) {
            $productIds[] = $product->getId();
        }

        return $productIds;
    }
}
