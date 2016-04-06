<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Eav\Model\Entity\Attribute;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class OptionManagement implements \Magento\Eav\Api\AttributeOptionManagementInterface
{
    /**
     * @var \Magento\Eav\Model\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $resourceModel;

    /**
     * @param \Magento\Eav\Model\AttributeRepository $attributeRepository
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $resourceModel
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Eav\Model\AttributeRepository $attributeRepository,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $resourceModel
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->resourceModel = $resourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function add($entityType, $attributeCode, $option)
    {
        if (empty($attributeCode)) {
            throw new InputException(__('Empty attribute code'));
        }

        $attribute = $this->attributeRepository->get($entityType, $attributeCode);
        if (!$attribute->usesSource()) {
            throw new StateException(__('Attribute %1 doesn\'t work with options', $attributeCode));
        }
        $key = 'new_option';

        $options = [];
        $options['value'][$key][0] = $option->getLabel();
        $options['order'][$key] = $option->getSortOrder();

        if (is_array($option->getStoreLabels())) {
            foreach ($option->getStoreLabels() as $label) {
                $options['value'][$key][$label->getStoreId()] = $label->getLabel();
            }
        }

        if ($option->getIsDefault()) {
            $attribute->setDefault([$key]);
        }

        $attribute->setOption($options);
        try {
            $this->resourceModel->save($attribute);
        } catch (\Exception $e) {
            throw new StateException(__('Cannot save attribute %1', $attributeCode));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entityType, $attributeCode, $optionId)
    {
        if (empty($attributeCode)) {
            throw new InputException(__('Empty attribute code'));
        }

        $attribute = $this->attributeRepository->get($entityType, $attributeCode);
        if (!$attribute->usesSource()) {
            throw new StateException(__('Attribute %1 doesn\'t have any option', $attributeCode));
        }

        if (!$attribute->getSource()->getOptionText($optionId)) {
            throw new NoSuchEntityException(
                __('Attribute %1 does not contain option with Id %2', $attribute->getId(), $optionId)
            );
        }

        $removalMarker = [
            'option' => [
                'value' => [$optionId => []],
                'delete' => [$optionId => '1'],
            ],
        ];
        $attribute->addData($removalMarker);
        try {
            $this->resourceModel->save($attribute);
        } catch (\Exception $e) {
            throw new StateException(__('Cannot save attribute %1', $attributeCode));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems($entityType, $attributeCode)
    {
        if (empty($attributeCode)) {
            throw new InputException(__('Empty attribute code'));
        }
        $attribute = $this->attributeRepository->get($entityType, $attributeCode);

        try {
            $options = $attribute->getOptions();
        } catch (\Exception $e) {
            throw new StateException(__('Cannot load options for attribute %1', $attributeCode));
        }

        return $options;
    }
}
