<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogSearch\Model\Adapter\Mysql\Field;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Framework\Search\Adapter\Mysql\Field\FieldFactory;
use Magento\Framework\Search\Adapter\Mysql\Field\FieldInterface;
use Magento\Framework\Search\Adapter\Mysql\Field\ResolverInterface;

class Resolver implements ResolverInterface
{
    /**
     * @var AttributeCollection
     */
    private $attributeCollection;
    /**
     * @var FieldFactory
     */
    private $fieldFactory;

    /**
     * @param AttributeCollection $attributeCollection
     * @param FieldFactory $fieldFactory
     */
    public function __construct(
        AttributeCollection $attributeCollection,
        FieldFactory $fieldFactory
    ) {
        $this->attributeCollection = $attributeCollection;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(array $fields)
    {
        $resolvedFields = [];
        foreach ($fields as $field) {
            if ('*' === $field) {
                $resolvedFields = [
                    $this->fieldFactory->create(
                        [
                            'attributeId' => null,
                            'column' => 'data_index',
                            'type' => FieldInterface::TYPE_FULLTEXT
                        ]
                    )
                ];
                break;
            }
            $attribute = $this->attributeCollection->getItemByColumnValue('attribute_code', $field);
            $attributeId = $attribute ? $attribute->getId() : 0;
            $resolvedFields[$field] = $this->fieldFactory->create(
                [
                    'attributeId' => $attributeId,
                    'column' => 'data_index',
                    'type' => FieldInterface::TYPE_FULLTEXT
                ]
            );
        }
        return $resolvedFields;
    }
}
