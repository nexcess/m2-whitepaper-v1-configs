<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Test\Unit\Model\Order;

use Magento\Sales\Model\Order\ItemRepository;

class ItemRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\DataObject\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Metadata|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadata;

    /**
     * @var \Magento\Sales\Api\Data\OrderItemSearchResultInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultFactory;

    /**
     * @var \Magento\Catalog\Model\ProductOptionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productOptionProcessorMock;

    /**
     * @var \Magento\Catalog\Model\ProductOptionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productOptionFactory;

    /**
     * @var \Magento\Catalog\Api\Data\ProductOptionExtensionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $extensionFactory;

    /**
     * @var array
     */
    protected $productOptionData = [];

    protected function setUp()
    {
        $this->objectFactory = $this->getMockBuilder('Magento\Framework\DataObject\Factory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->metadata = $this->getMockBuilder('Magento\Sales\Model\ResourceModel\Metadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchResultFactory = $this->getMockBuilder(
            'Magento\Sales\Api\Data\OrderItemSearchResultInterfaceFactory'
        )
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->productOptionFactory = $this->getMockBuilder('Magento\Catalog\Model\ProductOptionFactory')
            ->setMethods([
                'create',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionFactory = $this->getMockBuilder('Magento\Catalog\Api\Data\ProductOptionExtensionFactory')
            ->setMethods([
                'create',
            ])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage ID required
     */
    public function testGetWithNoId()
    {
        $model = new ItemRepository(
            $this->objectFactory,
            $this->metadata,
            $this->searchResultFactory,
            $this->productOptionFactory,
            $this->extensionFactory
        );

        $model->get(null);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Requested entity doesn't exist
     */
    public function testGetEmptyEntity()
    {
        $orderItemId = 1;

        $orderItemMock = $this->getMockBuilder('Magento\Sales\Model\Order\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemMock->expects($this->once())
            ->method('load')
            ->with($orderItemId)
            ->willReturn($orderItemMock);
        $orderItemMock->expects($this->once())
            ->method('getItemId')
            ->willReturn(null);

        $this->metadata->expects($this->once())
            ->method('getNewInstance')
            ->willReturn($orderItemMock);

        $model = new ItemRepository(
            $this->objectFactory,
            $this->metadata,
            $this->searchResultFactory,
            $this->productOptionFactory,
            $this->extensionFactory
        );

        $model->get($orderItemId);
    }

    public function testGet()
    {
        $orderItemId = 1;
        $productType = 'configurable';

        $this->productOptionData = ['option1' => 'value1'];

        $this->getProductOptionExtensionMock();
        $productOption = $this->getProductOptionMock();
        $orderItemMock = $this->getOrderMock($productType, $productOption);

        $orderItemMock->expects($this->once())
            ->method('load')
            ->with($orderItemId)
            ->willReturn($orderItemMock);
        $orderItemMock->expects($this->once())
            ->method('getItemId')
            ->willReturn($orderItemId);

        $this->metadata->expects($this->once())
            ->method('getNewInstance')
            ->willReturn($orderItemMock);

        $model = $this->getModel($orderItemMock, $productType);
        $this->assertSame($orderItemMock, $model->get($orderItemId));

        // Assert already registered
        $this->assertSame($orderItemMock, $model->get($orderItemId));
    }

    public function testGetList()
    {
        $productType = 'configurable';
        $field = 'field';
        $value = 'value';

        $this->productOptionData = ['option1' => 'value1'];

        $filterMock = $this->getMockBuilder('Magento\Framework\Api\Filter')
            ->disableOriginalConstructor()
            ->getMock();
        $filterMock->expects($this->once())
            ->method('getConditionType')
            ->willReturn(null);
        $filterMock->expects($this->once())
            ->method('getField')
            ->willReturn($field);
        $filterMock->expects($this->once())
            ->method('getValue')
            ->willReturn($value);

        $filterGroupMock = $this->getMockBuilder('Magento\Framework\Api\Search\FilterGroup')
            ->disableOriginalConstructor()
            ->getMock();
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterMock]);

        $searchCriteriaMock = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);

        $this->getProductOptionExtensionMock();
        $productOption = $this->getProductOptionMock();
        $orderItemMock = $this->getOrderMock($productType, $productOption);

        $searchResultMock = $this->getMockBuilder('Magento\Sales\Api\Data\OrderItemSearchResultInterface')
            ->setMethods([
                'addFieldToFilter',
                'getItems',
            ])
            ->getMockForAbstractClass();
        $searchResultMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with($field, ['eq' => $value])
            ->willReturnSelf();
        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$orderItemMock]);

        $this->searchResultFactory->expects($this->once())
            ->method('create')
            ->willReturn($searchResultMock);

        $model = $this->getModel($orderItemMock, $productType);
        $this->assertSame($searchResultMock, $model->getList($searchCriteriaMock));
    }

    public function testDeleteById()
    {
        $orderItemId = 1;
        $productType = 'configurable';

        $requestMock = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->getMock();

        $orderItemMock = $this->getMockBuilder('Magento\Sales\Model\Order\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemMock->expects($this->once())
            ->method('load')
            ->with($orderItemId)
            ->willReturn($orderItemMock);
        $orderItemMock->expects($this->once())
            ->method('getItemId')
            ->willReturn($orderItemId);
        $orderItemMock->expects($this->once())
            ->method('getProductType')
            ->willReturn($productType);
        $orderItemMock->expects($this->once())
            ->method('getBuyRequest')
            ->willReturn($requestMock);

        $orderItemResourceMock = $this->getMockBuilder('Magento\Framework\Model\ResourceModel\Db\AbstractDb')
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemResourceMock->expects($this->once())
            ->method('delete')
            ->with($orderItemMock)
            ->willReturnSelf();

        $this->metadata->expects($this->once())
            ->method('getNewInstance')
            ->willReturn($orderItemMock);
        $this->metadata->expects($this->exactly(1))
            ->method('getMapper')
            ->willReturn($orderItemResourceMock);

        $model = $this->getModel($orderItemMock, $productType);
        $this->assertTrue($model->deleteById($orderItemId));
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $orderItemMock
     * @param string $productType
     * @param array $data
     * @return ItemRepository
     */
    protected function getModel(
        \PHPUnit_Framework_MockObject_MockObject $orderItemMock,
        $productType,
        array $data = []
    ) {
        $requestMock = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->getMock();

        $requestUpdateMock = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->getMock();
        $requestUpdateMock->expects($this->any())
            ->method('getData')
            ->willReturn($data);

        $this->productOptionProcessorMock = $this->getMockBuilder(
            'Magento\Catalog\Model\ProductOptionProcessorInterface'
        )
            ->getMockForAbstractClass();
        $this->productOptionProcessorMock->expects($this->any())
            ->method('convertToProductOption')
            ->with($requestMock)
            ->willReturn($this->productOptionData);
        $this->productOptionProcessorMock->expects($this->any())
            ->method('convertToBuyRequest')
            ->with($orderItemMock)
            ->willReturn($requestUpdateMock);

        $model = new ItemRepository(
            $this->objectFactory,
            $this->metadata,
            $this->searchResultFactory,
            $this->productOptionFactory,
            $this->extensionFactory,
            [
                $productType => $this->productOptionProcessorMock,
                'custom_options' => $this->productOptionProcessorMock,
            ]
        );
        return $model;
    }

    /**
     * @param string $productType
     * @param \PHPUnit_Framework_MockObject_MockObject $productOption
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getOrderMock($productType, $productOption)
    {
        $requestMock = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->getMock();

        $orderItemMock = $this->getMockBuilder('Magento\Sales\Model\Order\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemMock->expects($this->once())
            ->method('getProductType')
            ->willReturn($productType);
        $orderItemMock->expects($this->once())
            ->method('getBuyRequest')
            ->willReturn($requestMock);
        $orderItemMock->expects($this->any())
            ->method('getProductOption')
            ->willReturn(null);
        $orderItemMock->expects($this->any())
            ->method('setProductOption')
            ->with($productOption)
            ->willReturnSelf();

        return $orderItemMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getProductOptionMock()
    {
        $productOption = $this->getMockBuilder('Magento\Catalog\Api\Data\ProductOptionInterface')
            ->getMockForAbstractClass();
        $productOption->expects($this->any())
            ->method('getExtensionAttributes')
            ->willReturn(null);

        $this->productOptionFactory->expects($this->any())
            ->method('create')
            ->willReturn($productOption);

        return $productOption;
    }

    protected function getProductOptionExtensionMock()
    {
        $productOptionExtension = $this->getMockBuilder('Magento\Catalog\Api\Data\ProductOptionExtensionInterface')
            ->setMethods([
                'setData',
            ])
            ->getMockForAbstractClass();
        $productOptionExtension->expects($this->any())
            ->method('setData')
            ->with(key($this->productOptionData), current($this->productOptionData))
            ->willReturnSelf();

        $this->extensionFactory->expects($this->any())
            ->method('create')
            ->willReturn($productOptionExtension);
    }
}
