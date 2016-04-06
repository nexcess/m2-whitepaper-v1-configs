<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Swatches\Test\Unit\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Helper\Image */
    protected $imageHelperMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory */
    protected $productCollectionFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\ResourceModel\Product\Collection */
    protected $productCollectionMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\ConfigurableProduct\Model\Product\Type\Configurable */
    protected $configurableMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\ProductFactory */
    protected $productModelFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product */
    protected $productMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\StoreManager */
    protected $storeManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory */
    protected $swatchCollectionFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\ResourceModel\Eav\Attribute */
    protected $attributeMock;

    /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager */
    protected $objectManager;

    /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager|\Magento\Swatches\Helper\Data */
    protected $swatchHelperObject;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Api\ProductRepositoryInterface */
    protected $productRepoMock;

    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->imageHelperMock = $this->getMock('\Magento\Catalog\Helper\Image', [], [], '', false);
        $this->productCollectionFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);

        $this->productCollectionMock = $this->objectManager->getCollectionMock(
            '\Magento\Catalog\Model\ResourceModel\Product\Collection',
            [
                $this->productMock,
                $this->productMock,
            ]
        );

        $this->configurableMock = $this->getMock(
            '\Magento\ConfigurableProduct\Model\Product\Type\Configurable',
            [],
            [],
            '',
            false
        );
        $this->productModelFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->productRepoMock = $this->getMock('\Magento\Catalog\Api\ProductRepositoryInterface', [], [], '', false);

        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManager', [], [], '', false);
        $this->swatchCollectionFactoryMock = $this->getMock(
            '\Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->attributeMock = $this->getMock('\Magento\Catalog\Model\ResourceModel\Eav\Attribute', [], [], '', false);

        $this->swatchHelperObject = $this->objectManager->getObject(
            '\Magento\Swatches\Helper\Data',
            [
                'productCollectionFactory' => $this->productCollectionFactoryMock,
                'configurable' => $this->configurableMock,
                'productRepository' => $this->productRepoMock,
                'storeManager' => $this->storeManagerMock,
                'swatchCollectionFactory' => $this->swatchCollectionFactoryMock,
                'imageHelper' => $this->imageHelperMock,
            ]
        );
    }

    /**
     * @deprecated
     *
     * @dataProvider dataForAdditionalData
     */
    public function testPopulateAdditionalDataEavAttribute($data, $count)
    {
        $this->attributeMock
            ->expects($this->exactly($count['getData']))
            ->method('getData')
            ->with('additional_data')
            ->will($this->returnValue($data));

        $this->attributeMock
            ->expects($this->exactly($count['setData']))
            ->method('setData');

        $this->swatchHelperObject->populateAdditionalDataEavAttribute($this->attributeMock);
    }

    public function dataForAdditionalData()
    {
        $additionalData = [
            'swatch_input_type' => 'visual',
            'update_product_preview_image' => 1,
            'use_product_image_for_swatch' => 0
        ];
        return [
            [
                serialize($additionalData),
                [
                    'getData' => 1,
                    'setData' => 3,
                ]
            ],
            [
                null,
                [
                    'getData' => 1,
                    'setData' => 0,
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataForAssembleEavAttribute
     */
    public function testAssembleAdditionalDataEavAttribute($dataFromDb, $attributeData)
    {
        $this->attributeMock
            ->expects($this->at(0))
            ->method('getData')
            ->with('additional_data')
            ->will($this->returnValue($dataFromDb));

        $i = 1;
        foreach ($attributeData as $key => $value) {
            $this->attributeMock
                ->expects($this->at($i))
                ->method('getData')
                ->with($key)
                ->willReturn($value);
            $i++;
        }

        $this->attributeMock->expects($this->once())->method('setData');

        $this->swatchHelperObject->assembleAdditionalDataEavAttribute($this->attributeMock);
    }

    public function dataForAssembleEavAttribute()
    {
        $additionalData = [
            'swatch_input_type' => 'visual',
            'update_product_preview_image' => 1,
            'use_product_image_for_swatch' => 0
        ];
        return [
            [
                serialize($additionalData),
                [
                    'swatch_input_type' => 'visual',
                    'update_product_preview_image' => 1,
                    'use_product_image_for_swatch' => 1,
                ],
            ],
            [
                null,
                [
                    'swatch_input_type' => null,
                    'update_product_preview_image' => 0,
                    'use_product_image_for_swatch' => 0,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataForVariationWithSwatchImage
     */
    public function testLoadFirstVariationSwatchImage($imageTypes, $expected, $requiredAttributes)
    {
        $this->getSwatchAttributes($this->productMock);
        $this->getUsedProducts($imageTypes + $requiredAttributes);

        $result = $this->swatchHelperObject->loadFirstVariationSwatchImage($this->productMock, $requiredAttributes);

        if ($expected === false) {
            $this->assertFalse($result);
        } else {
            $this->assertInstanceOf('\Magento\Catalog\Model\Product', $result);
        }
    }

    public function dataForVariationWithSwatchImage()
    {
        return [
            [
                [
                    'image' => '/m/a/magento.png',
                    'small_image' => '/m/a/magento.png',
                    'thumbnail' => '/m/a/magento.png',
                    'swatch_image' => '/m/a/magento.png', //important
                ],
                '\Magento\Catalog\Model\Product',
                ['color' => 31]
            ],
            [
                [
                    'image' => '/m/a/magento.png',
                    'small_image' => '/m/a/magento.png',
                    'thumbnail' => '/m/a/magento.png',
                ],
                false,
                ['size' => 31]
            ],
        ];
    }

    /**
     * @dataProvider dataForCreateSwatchProductByFallback
     */
    public function testLoadVariationByFallback($product)
    {
        $this->getSwatchAttributes($product);

        $this->prepareVariationCollection();

        $this->productCollectionMock->method('getFirstItem')->willReturn($this->productMock);
        $this->productMock->method('getId')->willReturn(95);
        $this->productModelFactoryMock->method('create')->willReturn($this->productMock);
        $this->productMock->method('load')->with(95)->will($this->returnSelf());

        $this->swatchHelperObject->loadVariationByFallback($this->productMock, ['color' => 31]);
    }

    /**
     * @dataProvider dataForVariationWithImage
     */
    public function testLoadFirstVariationImage($imageTypes, $expected, $requiredAttributes)
    {
        $this->getSwatchAttributes($this->productMock);
        $this->getUsedProducts($imageTypes + $requiredAttributes);

        $result = $this->swatchHelperObject->loadFirstVariationImage($this->productMock, $requiredAttributes);

        if ($expected === false) {
            $this->assertFalse($result);
        } else {
            $this->assertInstanceOf('\Magento\Catalog\Model\Product', $result);
        }
    }

    public function dataForVariationWithImage()
    {
        return [
            [
                [
                    'image' => '/m/a/magento.png', //important
                    'small_image' => '/m/a/magento.png',
                    'thumbnail' => '/m/a/magento.png',
                    'swatch_image' => '/m/a/magento.png',
                ],
                '\Magento\Catalog\Model\Product',
                ['color' => 31]
            ],
            [
                [
                    'small_image' => '/m/a/magento.png',
                    'thumbnail' => '/m/a/magento.png',
                    'swatch_image' => '/m/a/magento.png',
                ],
                false,
                ['size' => 31]
            ],
        ];
    }

    public function testLoadFirstVariationWithImageNoProduct()
    {
        $result = $this->swatchHelperObject->loadVariationByFallback($this->productMock, ['color' => 31]);
        $this->assertFalse($result);
    }

    public function testLoadVariationByFallbackWithoutProduct()
    {
        $result = $this->swatchHelperObject->loadFirstVariationImage($this->productMock, ['color' => 31]);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataForMediaGallery
     */
    public function testGetProductMediaGallery($mediaGallery, $image)
    {
        $this->productMock->expects($this->once())->method('getMediaAttributeValues')->willReturn($mediaGallery);
        $this->productMock->expects($this->any())->method('getId')->willReturn(95);

        $this->imageHelperMock->expects($this->any())
            ->method('init')
            ->willReturnMap([
                [$this->productMock, 'product_page_image_large', [], $this->imageHelperMock],
                [$this->productMock, 'product_page_image_medium', [], $this->imageHelperMock],
                [$this->productMock, 'product_page_image_small', [], $this->imageHelperMock],
            ]);

        $this->imageHelperMock->expects($this->any())
            ->method('setImageFile')
            ->with($image)
            ->willReturnSelf();
        $this->imageHelperMock->expects($this->any())
            ->method('constrainOnly')
            ->willReturnSelf();
        $this->imageHelperMock->expects($this->any())
            ->method('keepAspectRatio')
            ->willReturnSelf();
        $this->imageHelperMock->expects($this->any())
            ->method('keepFrame')
            ->willReturnSelf();
        $this->imageHelperMock->expects($this->any())
            ->method('getUrl')
            ->willReturn('http://full_path_to_image/magento1.png');

        $this->productRepoMock->expects($this->any())
            ->method('getById')
            ->with(95)
            ->willReturn($this->productMock);

        $mediaObject = $this->getMock('\Magento\Framework\DataObject', [], [], '', false);
        $iterator = new \ArrayIterator([$mediaObject]);
        $mediaCollectionMock = $this->getMock('\Magento\Framework\Data\Collection', [], [], '', false);
        $mediaCollectionMock->expects($this->any())->method('getIterator')->willReturn($iterator);
        $mediaObject->method('getData')->withConsecutive(
            ['value_id'],
            ['file']
        )->willReturnOnConsecutiveCalls(
            0,
            $image
        );
        $this->productMock->method('getMediaGalleryImages')->willReturn($mediaCollectionMock);

        $this->swatchHelperObject->getProductMediaGallery($this->productMock);
    }

    public function dataForMediaGallery()
    {
        return [
            [
                [
                    'image' => '/m/a/magento1.png',
                    'small_image' => '/m/a/magento2.png',
                    'thumbnail' => '/m/a/magento3.png',
                    'swatch_image' => '/m/a/magento4.png',
                ],
                '/m/a/magento1.png'
            ],
            [
                [
                    'small_image' => '/m/a/magento4.png',
                    'thumbnail' => '/m/a/magento5.png',
                    'swatch_image' => '/m/a/magento6.png',
                ],
                '/m/a/magento4.png'
            ],
            [
                [],
                ''
            ],
        ];
    }

    protected function getSwatchAttributes()
    {
        $this->getAttributesFromConfigurable();
        $this->attributeMock->method('hasData')->with('swatch_input_type')->willReturn(true);
        $this->attributeMock->method('getData')->with('swatch_input_type')->willReturn('visual');
    }

    protected function getUsedProducts(array $attributes)
    {
        $this->productMock
            ->expects($this->atLeastOnce())
            ->method('getTypeInstance')
            ->willReturn($this->configurableMock);

        $product1 = $this->getMock('\Magento\Catalog\Model\Product', ['hasData'], [], '', false);
        $product1->setData($attributes);

        $product2 = $this->getMock('\Magento\Catalog\Model\Product', ['hasData'], [], '', false);
        $product2->setData($attributes);

        $simpleProducts = [$product2, $product1];

        $this->configurableMock->expects($this->once())->method('getUsedProducts')->with($this->productMock)
            ->willReturn($simpleProducts);

    }

    protected function getAttributesFromConfigurable()
    {
        $this->productMock
            ->expects($this->atLeastOnce())
            ->method('getTypeInstance')
            ->willReturn($this->configurableMock);

        $confAttribute = $this->getMock(
            '\Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute',
            [],
            [],
            '',
            false
        );

        $this->configurableMock
            ->expects($this->any())
            ->method('getConfigurableAttributes')
            ->with($this->productMock)
            ->willReturn([$confAttribute, $confAttribute]);

        $confAttribute
            ->expects($this->any())
            ->method('__call')
            ->with('getProductAttribute')
            ->willReturn($this->attributeMock);
    }

    protected function prepareVariationCollection()
    {
        $this->productCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->productCollectionMock);

        $this->addfilterByParent();
    }

    protected function addfilterByParent()
    {
        $this->productCollectionMock
            ->method('getTable')
            ->with('catalog_product_relation')
            ->willReturn('catalog_product_relation');

        $zendDbSelectMock = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);

        $this->productCollectionMock->method('getSelect')->willReturn($zendDbSelectMock);
        $zendDbSelectMock->method('join')->willReturn($zendDbSelectMock);
        $zendDbSelectMock->method('where')->willReturn($zendDbSelectMock);
    }

    public function dataForCreateSwatchProduct()
    {
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);

        return [
            [
                $productMock,
                [
                    'image' => '',
                    'small_image' => '',
                    'thumbnail' => '',
                    'swatch_image' => '',
                ]
            ],
            [
                $productMock,
                [
                    'small_image' => 'img1.png',
                    'thumbnail' => 'img1.png',
                ]
            ],
            [
                $productMock,
                []
            ],
            [
                $productMock,
                [
                    'image' => 'img1.png',
                    'small_image' => 'img1.png',
                    'thumbnail' => 'img1.png',
                ]
            ],
        ];
    }

    public function dataForCreateSwatchProductByFallback()
    {
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);

        return [
            [
                95,
            ],
            [
                $productMock,
            ],
        ];
    }

    /**
     * @dataProvider dataForGettingSwatchAsArray
     */
    public function testGetSwatchAttributesAsArray($optionsArray, $attributeData, $expected)
    {
        $configurable = $this->getMock(
            '\Magento\ConfigurableProduct\Model\Product\Type\Configurable',
            [],
            [],
            '',
            false
        );

        $this->productMock
            ->expects($this->atLeastOnce())
            ->method('getTypeInstance')
            ->willReturn($configurable);

        $confAttribute = $this->getMock(
            '\Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute',
            [],
            [],
            '',
            false
        );

        $configurable
            ->expects($this->atLeastOnce())
            ->method('getConfigurableAttributes')
            ->with($this->productMock)
            ->willReturn([$confAttribute]);

        $confAttribute
            ->expects($this->atLeastOnce())
            ->method('__call')
            ->with('getProductAttribute')
            ->willReturn($this->attributeMock);

        $this->attributeMock->method('setStoreId')->will($this->returnSelf());
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->method('getId')->willReturn(1);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $this->attributeMock->method('hasData')->with('swatch_input_type')->willReturn(true);
        $this->attributeMock->expects($this->at(1))->method('getData')->with('swatch_input_type')->willReturn('visual');
        $this->attributeMock->expects($this->at(3))->method('getData')->with('')->willReturn($attributeData);

        $sourceMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Source\AbstractSource', [], [], '', false);
        $sourceMock->expects($this->any())->method('getAllOptions')->with(false)->willReturn($optionsArray);
        $this->attributeMock->method('getSource')->willReturn($sourceMock);

        $result = $this->swatchHelperObject->getSwatchAttributesAsArray($this->productMock);
        $this->assertEquals($result, $expected);
    }

    public function dataForGettingSwatchAsArray()
    {
        return [
            [
                [
                    ['value' => 45, 'label' => 'green'],
                    ['value' => 46, 'label' => 'yellow'],
                    ['value' => 47, 'label' => 'red'],
                    ['value' => 48, 'label' => 'blue'],
                ],
                [
                    'attribute_id' => 52
                ],
                [
                    52 => [
                        'attribute_id' => 52,
                        'options' => [
                            45 => 'green',
                            46 => 'yellow',
                            47 => 'red',
                            48 => 'blue',
                        ],
                    ]
                ],
            ],
            [
                [
                    ['value' => 45, 'label' => 'green'],
                    ['value' => 46, 'label' => 'yellow'],
                ],
                [
                    'attribute_id' => 324
                ],
                [
                    324 => [
                        'attribute_id' => 324,
                        'options' => [
                            45 => 'green',
                            46 => 'yellow',
                        ],
                    ]
                ],
            ],
        ];
    }

    public function testGetSwatchesByOptionsIdIf1()
    {
        $swatchMock = $this->getMock('\Magento\Swatches\Model\Swatch', [], [], '', false);

        $optionsData = [
            [
                'type' => 1,
                'store_id' => 1,
                'value' => '#324234',
                'option_id' => 35,
                'id' => 423,
            ],
        ];

        $swatchMock->expects($this->at(0))->method('offsetGet')->with('type')->willReturn(1);
        $swatchMock->expects($this->at(1))->method('offsetGet')->with('option_id')->willReturn(35);
        $swatchMock->expects($this->at(2))->method('getData')->with('')->willReturn($optionsData[0]);

        $swatchCollectionMock = $this->objectManager->getCollectionMock(
            '\Magento\Swatches\Model\ResourceModel\Swatch\Collection',
            [
                $swatchMock,
            ]
        );
        $this->swatchCollectionFactoryMock->method('create')->willReturn($swatchCollectionMock);

        $swatchCollectionMock->method('addFilterByOptionsIds')->with([35])->will($this->returnSelf());

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);
        $storeMock->method('getId')->willReturn(1);

        $this->swatchHelperObject->getSwatchesByOptionsId([35]);
    }

    public function testGetSwatchesByOptionsIdIf2()
    {
        $swatchMock = $this->getMock('\Magento\Swatches\Model\Swatch', [], [], '', false);

        $optionsData = [
            [
                'type' => 0,
                'store_id' => 1,
                'value' => 'test',
                'option_id' => 35,
                'id' => 487,
            ],
            [
                'type' => 0,
                'store_id' => 1,
                'value' => 'test2',
                'option_id' => 36,
                'id' => 488,
            ]
        ];

        $swatchMock->expects($this->at(0))->method('offsetGet')->with('type')->willReturn(0);
        $swatchMock->expects($this->at(1))->method('offsetGet')->with('store_id')->willReturn(1);
        $swatchMock->expects($this->at(2))->method('offsetGet')->with('value')->willReturn('test');
        $swatchMock->expects($this->at(3))->method('offsetGet')->with('option_id')->willReturn(35);
        $swatchMock->expects($this->at(4))->method('getData')->with('')->willReturn($optionsData[0]);
        $swatchMock->expects($this->at(5))->method('offsetGet')->with('type')->willReturn(0);
        $swatchMock->expects($this->at(6))->method('offsetGet')->with('store_id')->willReturn(1);
        $swatchMock->expects($this->at(7))->method('offsetGet')->with('value')->willReturn('test2');
        $swatchMock->expects($this->at(8))->method('offsetGet')->with('option_id')->willReturn(36);
        $swatchMock->expects($this->at(9))->method('getData')->with('')->willReturn($optionsData[1]);

        $swatchCollectionMock = $this->objectManager->getCollectionMock(
            '\Magento\Swatches\Model\ResourceModel\Swatch\Collection',
            [
                $swatchMock,
                $swatchMock,
            ]
        );
        $this->swatchCollectionFactoryMock->method('create')->willReturn($swatchCollectionMock);

        $swatchCollectionMock->method('addFilterByOptionsIds')->with([35])->will($this->returnSelf());

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);
        $storeMock->method('getId')->willReturn(1);

        $this->swatchHelperObject->getSwatchesByOptionsId([35]);
    }

    public function testGetSwatchesByOptionsIdIf3()
    {
        $swatchMock = $this->getMock('\Magento\Swatches\Model\Swatch', [], [], '', false);

        $optionsData = [
            'type' => 0,
            'store_id' => 0,
            'value' => 'test_test',
            'option_id' => 35,
            'id' => 423,
        ];

        $swatchMock->expects($this->at(0))->method('offsetGet')->with('type')->willReturn(0);
        $swatchMock->expects($this->at(1))->method('offsetGet')->with('store_id')->willReturn(0);
        $swatchMock->expects($this->at(2))->method('offsetGet')->with('store_id')->willReturn(0);
        $swatchMock->expects($this->at(3))->method('offsetGet')->with('option_id')->willReturn(35);
        $swatchMock->expects($this->at(4))->method('getData')->with('')->willReturn($optionsData);

        $swatchCollectionMock = $this->objectManager->getCollectionMock(
            '\Magento\Swatches\Model\ResourceModel\Swatch\Collection',
            [
                $swatchMock,
            ]
        );
        $this->swatchCollectionFactoryMock->method('create')->willReturn($swatchCollectionMock);

        $swatchCollectionMock->method('addFilterByOptionsIds')->with([35])->will($this->returnSelf());

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);
        $storeMock->method('getId')->willReturn(1);

        $this->swatchHelperObject->getSwatchesByOptionsId([35]);
    }

    public function testIsProductHasSwatch()
    {
        $this->getSwatchAttributes();
        $result = $this->swatchHelperObject->isProductHasSwatch($this->productMock);
        $this->assertEquals(2, $result);
    }

    /**
     * @dataProvider dataIsVisualSwatch
     */
    public function testIsVisualSwatch($swatchType, $boolResult)
    {
        $this->attributeMock->method('hasData')->with('swatch_input_type')->willReturn(true);
        $this->attributeMock
            ->expects($this->once())
            ->method('getData')
            ->with('swatch_input_type')
            ->willReturn($swatchType);
        $result = $this->swatchHelperObject->isVisualSwatch($this->attributeMock);
        if ($boolResult) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function dataIsVisualSwatch()
    {
        return [
            [
                'visual',
                true,
            ],
            [
                'some_other_type',
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataForIsVisualSwatchType
     */
    public function testIsVisualSwatchFalse($data, $count, $swatchType, $boolResult)
    {
        $this->attributeMock->method('hasData')->with('swatch_input_type')->willReturn(false);

        $this->attributeMock->expects($this->exactly(2))->method('getData')->withConsecutive(
            ['additional_data'],
            ['swatch_input_type']
        )->willReturnOnConsecutiveCalls(
            $data,
            $swatchType
        );

        $this->attributeMock
            ->expects($this->exactly($count['setData']))
            ->method('setData');

        $result = $this->swatchHelperObject->isVisualSwatch($this->attributeMock);
        if ($boolResult) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function dataForIsVisualSwatchType()
    {
        $additionalData = [
            'swatch_input_type' => 'visual',
            'update_product_preview_image' => 1,
            'use_product_image_for_swatch' => 0
        ];
        return [
            [
                serialize($additionalData),
                [
                    'getData' => 1,
                    'setData' => 3,
                ],
                'visual',
                true,
            ],
            [
                null,
                [
                    'getData' => 1,
                    'setData' => 0,
                ],
                'some_other_type',
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataForIsTextSwatchType
     */
    public function testIsTextSwatchFalse($data, $count, $swatchType, $boolResult)
    {
        $this->attributeMock->method('hasData')->with('swatch_input_type')->willReturn(false);

        $this->attributeMock->expects($this->exactly(2))->method('getData')->withConsecutive(
            ['additional_data'],
            ['swatch_input_type']
        )->willReturnOnConsecutiveCalls(
            $data,
            $swatchType
        );

        $this->attributeMock
            ->expects($this->exactly($count['setData']))
            ->method('setData');

        $result = $this->swatchHelperObject->isTextSwatch($this->attributeMock);
        if ($boolResult) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function dataForIsTextSwatchType()
    {
        $additionalData = [
            'swatch_input_type' => 'text',
            'update_product_preview_image' => 1,
            'use_product_image_for_swatch' => 0
        ];
        return [
            [
                serialize($additionalData),
                [
                    'getData' => 1,
                    'setData' => 3,
                ],
                'text',
                true,
            ],
            [
                null,
                [
                    'getData' => 1,
                    'setData' => 0,
                ],
                'some_other_type',
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataIsTextSwatch
     */
    public function testIsTextSwatch($swatchType, $boolResult)
    {
        $this->attributeMock->method('hasData')->with('swatch_input_type')->willReturn(true);
        $this->attributeMock
            ->expects($this->once())
            ->method('getData')
            ->with('swatch_input_type')
            ->willReturn($swatchType);
        $result = $this->swatchHelperObject->isTextSwatch($this->attributeMock);
        if ($boolResult) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function dataIsTextSwatch()
    {
        return [
            [
                'text',
                true,
            ],
            [
                'some_other_type',
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataIsSwatchAttribute
     */
    public function testIsSwatchAttribute($times, $swatchType, $boolResult)
    {
        $this->attributeMock->method('hasData')->with('swatch_input_type')->willReturn(true);
        $this->attributeMock
            ->expects($this->exactly($times))
            ->method('getData')
            ->with('swatch_input_type')
            ->willReturn($swatchType);

        $result = $this->swatchHelperObject->isSwatchAttribute($this->attributeMock);
        if ($boolResult) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function dataIsSwatchAttribute()
    {
        return [
            [
                1,
                'visual',
                true,
            ],
            [
                2,
                'text',
                true,
            ],
            [
                2,
                'some_other_type',
                false,
            ],
        ];
    }
}
