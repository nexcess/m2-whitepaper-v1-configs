<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Test\Unit\Model\Product\TypeHandler;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Test for \Magento\Downloadable\Model\Product\TypeHandler\Sample
 */
class SampleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Downloadable\Model\ResourceModel\Link|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sampleResource;

    /**
     * @var \Magento\Downloadable\Model\SampleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sampleFactory;

    /**
     * @var \Magento\Downloadable\Model\Product\TypeHandler\Sample
     */
    private $target;

    protected function setUp()
    {
        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->sampleFactory = $this->getMockBuilder('\Magento\Downloadable\Model\SampleFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->sampleResource = $this->getMockBuilder('\Magento\Downloadable\Model\ResourceModel\Sample')
            ->disableOriginalConstructor()
            ->setMethods(['deleteItems'])
            ->getMock();
        $sampleResourceFactory = $this->getMockBuilder('\Magento\Downloadable\Model\ResourceModel\SampleFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $sampleResourceFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->sampleResource));
        $this->target = $objectManagerHelper->getObject(
            'Magento\Downloadable\Model\Product\TypeHandler\Sample',
            [
                'sampleFactory' => $this->sampleFactory,
                'sampleResourceFactory' => $sampleResourceFactory,
            ]
        );
    }

    /**
     * @dataProvider saveDataProvider
     * @param \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product
     * @param array $data
     * @param array $modelData
     */
    public function testSave($product, array $data, array $modelData)
    {
        $link = $this->createSampleModel($product, $modelData, true);
        $this->sampleFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($link));
        $this->target->save($product, $data);
    }

    /**
     * @return array
     */
    public function saveDataProvider()
    {
        return [
            [
                'product' => $this->createProductMock(100500, 1, 10, [10]),
                'data' => [
                    'sample' => [
                        [
                            'is_delete' => 0,
                            'sample_id' => 0,
                            'title' => 'Downloadable Product Sample Title',
                            'type' => \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE,
                            'sample_url' => null,
                            'sort_order' => '0',
                        ],
                    ],
                ],
                'modelData' => [
                    'title' => 'Downloadable Product Sample Title',
                    'type' => \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE,
                    'sample_url' => null,
                    'sort_order' => '0',
                ]
            ]
        ];
    }

    /**
     * @param \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product
     * @param array $data
     * @param array $expectedItems
     * @dataProvider deleteDataProvider
     */
    public function testDelete($product, array $data, array $expectedItems)
    {
        $this->sampleResource->expects($this->once())
            ->method('deleteItems')
            ->with($this->equalTo($expectedItems));
        $this->target->save($product, $data);
    }

    /**
     * @return array
     */
    public function deleteDataProvider()
    {
        return [
            [
                'product' => $this->createProductMock(1, 1, 1, [1]),
                'data' => [
                    'sample' => [
                        [
                            'sample_id' => 1,
                            'is_delete' => 1,
                        ],
                        [
                            'sample_id' => 2,
                            'is_delete' => 1,
                        ],
                        [
                            'sample_id' => null,
                            'is_delete' => 1,
                        ],
                        [
                            'sample_id' => false,
                            'is_delete' => 1,
                        ],
                        [
                            'sample_id' => 456,
                            'is_delete' => 1,
                        ],
                    ]
                ],
                'expectedItems' => [1, 2, 456]
            ]
        ];
    }

    /**
     * @param \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product
     * @param array $modelData
     * @return \Magento\Downloadable\Model\Sample|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createSampleModel($product, array $modelData)
    {
        $sample = $this->getMockBuilder('\Magento\Downloadable\Model\Sample')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setData',
                    'setSampleType',
                    'setProductId',
                    'setStoreId',
                    'setProductWebsiteIds',
                    'setNumberOfDownloads',
                    'setSampleUrl',
                    'setLinkFile',
                    'setSampleFile',
                    'save',
                ]
            )
            ->getMock();
        $sample->expects($this->once())
            ->method('setData')
            ->with($modelData)
            ->will($this->returnSelf());
        $sample->expects($this->once())
            ->method('setSampleType')
            ->with($modelData['type'])
            ->will($this->returnSelf());
        $sample->expects($this->once())
            ->method('setProductId')
            ->with($product->getId())
            ->will($this->returnSelf());
        $sample->expects($this->once())
            ->method('setStoreId')
            ->with($product->getStoreId())
            ->will($this->returnSelf());

        return $sample;
    }

    /**
     * @param int $id
     * @param int $storeId
     * @param int $storeWebsiteId
     * @param array $websiteIds
     * @return \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     * @internal param bool $isUnlimited
     */
    private function createProductMock($id, $storeId, $storeWebsiteId, array $websiteIds)
    {
        $product = $this->getMockBuilder('\Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getStoreId', 'getStore', 'getWebsiteIds'])
            ->getMock();
        $product->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));
        $product->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        $product->expects($this->any())
            ->method('getWebsiteIds')
            ->will($this->returnValue($websiteIds));
        $store = $this->getMockBuilder('\Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['getWebsiteId'])
            ->getMock();
        $store->expects($this->any())
            ->method('getWebsiteId')
            ->will($this->returnValue($storeWebsiteId));
        $product->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));
        return $product;
    }
}
