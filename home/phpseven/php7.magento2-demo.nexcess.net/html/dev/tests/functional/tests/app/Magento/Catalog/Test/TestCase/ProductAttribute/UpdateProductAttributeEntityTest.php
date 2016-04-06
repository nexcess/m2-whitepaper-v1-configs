<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * Dataset : AttributeOptions
 * 1. Attribute is created (Attribute)
 * 2. Attribute set is created (Attribute Set)
 *
 * Test Flow:
 * 1. Log in to Backend.
 * 2. Navigate to Stores > Attributes > Product.
 * 3. Select created attribute from preconditions
 * 4. Fill data from dataset
 * 5. Click 'Save Attribute' button
 * 6. Perform all assertions
 *
 * @group Product_Attributes_(MX)
 * @ZephyrId MAGETWO-23459
 */
class UpdateProductAttributeEntityTest extends Injectable
{
    /* tags */
    const MVP = 'yes';
    const DOMAIN = 'MX';
    /* end tags */

    /**
     * Factory for fixtures.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data.
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Run UpdateProductAttributeEntity test
     *
     * @param CatalogProductAttribute $productAttributeOriginal
     * @param CatalogProductAttribute $attribute
     * @param CatalogAttributeSet $attributeSet
     * @param CatalogProductAttributeIndex $attributeIndex
     * @param CatalogProductAttributeNew $attributeNew
     * @param CatalogProductSimple $productSimple
     * @return array
     */
    public function testUpdateProductAttribute(
        CatalogProductAttribute $productAttributeOriginal,
        CatalogProductAttribute $attribute,
        CatalogAttributeSet $attributeSet,
        CatalogProductAttributeIndex $attributeIndex,
        CatalogProductAttributeNew $attributeNew,
        CatalogProductSimple $productSimple
    ) {
        //Precondition
        $attributeSet->persist();
        $productAttributeOriginal->persist();

        $filter = [
            'attribute_code' => $productAttributeOriginal->getAttributeCode(),
        ];

        //Steps
        $attributeIndex->open();
        $attributeIndex->getGrid()->searchAndOpen($filter);
        $attributeNew->getAttributeForm()->fill($attribute);
        $attributeNew->getPageActions()->save();
        $attribute = $this->prepareAttribute($attribute, $productAttributeOriginal);
        $productSimple->persist();

        return ['product' => $this->prepareProduct($productSimple, $attribute, $attributeSet)];
    }

    /**
     * Prepare product data.
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductAttribute $attribute
     * @param CatalogAttributeSet $attributeSet
     * @return CatalogProductSimple
     */
    protected function prepareProduct($product, $attribute, $attributeSet)
    {
        $data = [
            'attribute_set_id' => ['attribute_set' => $attributeSet],
            'custom_attribute' => $attribute
        ];
        $data = array_merge($data, $product->getData());

        return $this->fixtureFactory->createByCode('catalogProductSimple', ['data' => $data]);
    }

    /**
     * Prepare attribute data.
     *
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductAttribute $productAttributeOriginal
     * @return CatalogProductAttribute
     */
    protected function prepareAttribute($attribute, $productAttributeOriginal)
    {
        $attributeData = array_merge($attribute->getData(), $productAttributeOriginal->getData());

        return $this->fixtureFactory->createByCode('catalogProductAttribute', ['data' => $attributeData]);
    }
}
