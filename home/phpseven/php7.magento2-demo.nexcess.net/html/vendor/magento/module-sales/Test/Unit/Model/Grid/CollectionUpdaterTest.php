<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Sales\Test\Unit\Model\Grid;

class CollectionUpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Grid\CollectionUpdater
     */
    protected $collectionUpdater;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    protected function setUp()
    {
        $this->registryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);

        $this->collectionUpdater = new \Magento\Sales\Model\Grid\CollectionUpdater(
            $this->registryMock
        );
    }

    public function testUpdateIfOrderNotExists()
    {
        $collectionMock = $this->getMock(
            'Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection', [], [], '', false
        );
        $this->registryMock
            ->expects($this->once())
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue(false));
        $collectionMock->expects($this->never())->method('setOrderFilter');
        $collectionMock
            ->expects($this->once())
            ->method('addOrderInformation')
            ->with(['increment_id'])
            ->will($this->returnSelf());
        $this->assertEquals($collectionMock, $this->collectionUpdater->update($collectionMock));
    }

    public function testUpdateIfOrderExists()
    {
        $collectionMock = $this->getMock(
            'Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection', [], [], '', false
        );
        $orderMock = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $this->registryMock
            ->expects($this->once())
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('getId')->will($this->returnValue('orderId'));
        $collectionMock->expects($this->once())->method('setOrderFilter')->with('orderId')->will($this->returnSelf());
        $collectionMock
            ->expects($this->once())
            ->method('addOrderInformation')
            ->with(['increment_id'])
            ->will($this->returnSelf());
        $this->assertEquals($collectionMock, $this->collectionUpdater->update($collectionMock));
    }
}
