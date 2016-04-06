<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogSearch\Test\Unit\Model\Search;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class RequestGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
    protected $objectManagerHelper;

    /** @var \Magento\CatalogSearch\Model\Search\RequestGenerator */
    protected $object;

    /** @var  CollectionFactory | \PHPUnit_Framework_MockObject_MockObject */
    protected $productAttributeCollectionFactory;

    public function setUp()
    {
        $this->productAttributeCollectionFactory =
            $this->getMockBuilder('Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory')
                ->setMethods(['create'])
                ->disableOriginalConstructor()
                ->getMock();

        $this->objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->object = $this->objectManagerHelper->getObject(
            'Magento\\CatalogSearch\\Model\\Search\\RequestGenerator',
            ['productAttributeCollectionFactory' => $this->productAttributeCollectionFactory]
        );
    }

    /**
     * @return array
     */
    public function attributesProvider()
    {
        return [
            [
                [
                    'quick_search_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0],
                    'advanced_search_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0],
                    'catalog_view_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0]
                ],
                ['sku', 'static', 0, 0, 1 ]
            ],
            [
                [
                    'quick_search_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0],
                    'advanced_search_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0],
                    'catalog_view_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0]
                ],
                ['price', 'static', 1, 0 ,1]
            ],
            [
                [
                    'quick_search_container' => ['queries' => 1, 'filters' => 0, 'aggregations' => 0],
                    'advanced_search_container' => ['queries' => 2, 'filters' => 0, 'aggregations' => 0],
                    'catalog_view_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0]
                ],
                ['name', 'text', 0, 0, 1]
            ],
            [
                [
                    'quick_search_container' => ['queries' => 1, 'filters' => 0, 'aggregations' => 0],
                    'advanced_search_container' => ['queries' => 2, 'filters' => 0, 'aggregations' => 0],
                    'catalog_view_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0]
                ],
                ['name2', 'text', 0, 0, 1]
            ],
            [
                [
                    'quick_search_container' => ['queries' => 3, 'filters' => 1, 'aggregations' => 1],
                    'advanced_search_container' => ['queries' => 2, 'filters' => 1, 'aggregations' => 0],
                    'catalog_view_container' => ['queries' => 2, 'filters' => 1, 'aggregations' => 1]
                ],
                ['date', 'decimal', 1, 1, 1]
            ],
            [
                [
                    'quick_search_container' => ['queries' => 3, 'filters' => 1, 'aggregations' => 1],
                    'advanced_search_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0],
                    'catalog_view_container' => ['queries' => 0, 'filters' => 0, 'aggregations' => 0]
                ],
                ['attr_int', 'int', 0, 1, 0]
            ],
        ];
    }

    /**
     * @param array $countResult
     * @param $attributeOptions
     * @dataProvider attributesProvider
     */
    public function testGenerate($countResult, $attributeOptions)
    {
        $collection = $this->getMockBuilder('Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $collection->expects($this->any())
            ->method('getIterator')
            ->willReturn(
                new \ArrayIterator(
                    [
                        $this->createAttributeMock($attributeOptions),
                    ]
                )
            );
        $collection->expects($this->any())
            ->method('addFieldToFilter')
            ->with(
                ['is_searchable', 'is_visible_in_advanced_search', 'is_filterable', 'is_filterable_in_search'],
                [1, 1, [1, 2], 1]
            )
            ->will($this->returnSelf());

        $this->productAttributeCollectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($collection);
        $result = $this->object->generate();

        $this->assertEquals(
            $countResult['quick_search_container']['queries'],
            $this->countVal($result['quick_search_container']['queries'])
        );
        $this->assertEquals(
            $countResult['advanced_search_container']['queries'],
            $this->countVal($result['advanced_search_container']['queries'])
        );
        $this->assertEquals(
            $countResult['advanced_search_container']['filters'],
            $this->countVal($result['advanced_search_container']['filters'])
        );
        $this->assertEquals(
            $countResult['catalog_view_container']['queries'],
            $this->countVal($result['catalog_view_container']['queries'])
        );
    }

    /**
     * Create attribute mock
     *
     * @param $attributeOptions
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createAttributeMock($attributeOptions)
    {
        $attribute = $this->getMockBuilder('Magento\Catalog\Model\ResourceModel\Product\Attribute')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getAttributeCode',
                    'getBackendType',
                    'getIsVisibleInAdvancedSearch',
                    'getSearchWeight',
                    'getFrontendInput',
                    'getData',
                    'getIsSearchable',
                ]
            )
            ->getMock();
        $attribute->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn($attributeOptions[0]);
        $attribute->expects($this->any())
            ->method('getBackendType')
            ->willReturn($attributeOptions[1]);
        $attribute->expects($this->any())
            ->method('getFrontendInput')
            ->willReturn($attributeOptions[1]);

        $attribute->expects($this->any())
            ->method('getSearchWeight')
            ->willReturn(1);

        $attribute->expects($this->any())
            ->method('getIsVisibleInAdvancedSearch')
            ->willReturn($attributeOptions[4]);

        $attribute->expects($this->any())
            ->method('getData')
            ->willReturnMap(
                [
                    ['is_filterable', $attributeOptions[2]],
                    ['is_filterable_in_search', $attributeOptions[3]]
                ]
            );

        $attribute->expects($this->any())
            ->method('getIsSearchable')
            ->willReturn(1);

        return $attribute;
    }

    private function countVal(&$value)
    {
        return !empty($value) ? count($value) : 0;
    }
}
