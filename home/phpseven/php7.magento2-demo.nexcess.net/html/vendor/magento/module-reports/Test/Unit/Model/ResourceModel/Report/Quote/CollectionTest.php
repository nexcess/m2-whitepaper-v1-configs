<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reports\Test\Unit\Model\ResourceModel\Report\Quote;

use Magento\Framework\App\ResourceConnection;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Reports\Model\ResourceModel\Quote\Collection as Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectMock;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->selectMock = $this->getMock('\Magento\Framework\DB\Select', [], [], '', false);
    }

    public function testGetSelectCountSql()
    {
        /** @var $collection \PHPUnit_Framework_MockObject_MockObject */
        $constructArgs = $this->objectManager
            ->getConstructArguments('Magento\Reports\Model\ResourceModel\Quote\Collection');
        $collection = $this->getMock(
            'Magento\Reports\Model\ResourceModel\Quote\Collection',
            ['getSelect'],
            $constructArgs,
            '',
            false
        );

        $collection->expects($this->once())->method('getSelect')->willReturn($this->selectMock);
        $this->selectMock->expects($this->atLeastOnce())->method('reset')->willReturnSelf();
        $this->selectMock->expects($this->once())
            ->method('columns')
            ->with('COUNT(*)')
            ->willReturnSelf();
        $this->assertEquals($this->selectMock, $collection->getSelectCountSql());
    }

    public function testPrepareActiveCartItems()
    {
        /** @var $collection \PHPUnit_Framework_MockObject_MockObject */
        $constructArgs = $this->objectManager
            ->getConstructArguments('Magento\Reports\Model\ResourceModel\Quote\Item\Collection');
        $collection = $this->getMock(
            'Magento\Reports\Model\ResourceModel\Quote\Item\Collection',
            ['getSelect', 'getTable'],
            $constructArgs,
            '',
            false
        );

        $collection->expects($this->once())->method('getSelect')->willReturn($this->selectMock);
        $this->selectMock->expects($this->once())->method('reset')->willReturnSelf();
        $this->selectMock->expects($this->once())->method('from')->willReturnSelf();
        $this->selectMock->expects($this->atLeastOnce())->method('columns')->willReturnSelf();
        $this->selectMock->expects($this->once())->method('joinInner')->willReturnSelf();
        $this->selectMock->expects($this->once())->method('where')->willReturnSelf();
        $this->selectMock->expects($this->once())->method('group')->willReturnSelf();
        $collection->expects($this->exactly(2))->method('getTable')->willReturn('table');
        $collection->prepareActiveCartItems();
    }

    public function testLoadWithFilter()
    {
        /** @var $collection \PHPUnit_Framework_MockObject_MockObject */
        $constructArgs = $this->objectManager
            ->getConstructArguments('Magento\Reports\Model\ResourceModel\Quote\Item\Collection');
        $constructArgs['eventManager'] = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $connectionMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $resourceMock = $this->getMock('\Magento\Quote\Model\ResourceModel\Quote', [], [], '', false);
        $resourceMock
            ->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', [], [], '', false));
        $constructArgs['resource'] = $resourceMock;
        $productResourceMock = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Product\Collection',
            [],
            [],
            '',
            false
        );
        $constructArgs['productResource'] = $productResourceMock;
        $orderResourceMock = $this->getMock('\Magento\Sales\Model\ResourceModel\Order\Collection', [], [], '', false);
        $constructArgs['orderResource'] = $orderResourceMock;

        $collection = $this->getMock(
            'Magento\Reports\Model\ResourceModel\Quote\Item\Collection',
            [
                '_beforeLoad',
                '_renderFilters',
                '_renderOrders',
                '_renderLimit',
                'printLogQuery',
                'getData',
                '_setIsLoaded',
                'setConnection',
                '_initSelect',
                'getTable',
                'getItems',
                'getOrdersData'
            ],
            $constructArgs
        );
        //load()
        $collection->expects($this->once())->method('_beforeLoad')->willReturnSelf();
        $collection->expects($this->once())->method('_renderFilters')->willReturnSelf();
        $collection->expects($this->once())->method('_renderOrders')->willReturnSelf();
        $collection->expects($this->once())->method('_renderLimit')->willReturnSelf();
        $collection->expects($this->once())->method('printLogQuery')->willReturnSelf();
        $collection->expects($this->once())->method('getData')->willReturn(null);
        $collection->expects($this->once())->method('_setIsLoaded')->willReturnSelf();

        //productLoad()
        $productAttributeMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            [],
            [],
            '',
            false
        );
        $priceAttributeMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            [],
            [],
            '',
            false
        );
        $productResourceMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($connectionMock);
        $productResourceMock->expects($this->any())
            ->method('getAttribute')
            ->willReturnMap([['name', $productAttributeMock], ['price', $priceAttributeMock]]);
        $productResourceMock->expects($this->once())->method('getSelect')->willReturn($this->selectMock);
        $this->selectMock->expects($this->once())->method('reset')->willReturnSelf();
        $this->selectMock->expects($this->once())->method('from')->willReturnSelf();
        $this->selectMock->expects($this->once())->method('useStraightJoin')->willReturnSelf();
        $this->selectMock->expects($this->exactly(2))->method('joinInner')->willReturnSelf();
        $collection->expects($this->once())->method('getOrdersData')->willReturn([]);

        $productAttributeMock->expects($this->once())->method('getBackend')->willReturnSelf();
        $priceAttributeMock->expects($this->once())->method('getBackend')->willReturnSelf();
        $connectionMock->expects($this->once())->method('fetchAssoc')->willReturn([1, 2, 3]);

        //_afterLoad()
        $collection->expects($this->once())->method('getItems')->willReturn([]);

        $collection->loadWithFilter();
    }
}
