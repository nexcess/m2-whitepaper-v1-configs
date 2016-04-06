<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Shipping\Test\Unit\Model;

use \Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ShipmentTest
 */
class ShipmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $shipment;

    public function setUp()
    {
        $this->orderRepository = $this->getMock(
            '\Magento\Sales\Api\OrderRepositoryInterface',
            [],
            [],
            '',
            false
        );

        $objectManagerHelper = new ObjectManagerHelper($this);
        $arguments = [
            'context' => $this->getMock('Magento\Framework\Model\Context', [], [], '', false),
            'registry' => $this->getMock('Magento\Framework\Registry', [], [], '', false),
            'localeDate' => $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface', [], [], '', false),
            'dateTime' => $this->getMock('Magento\Framework\Stdlib\DateTime', [], [], '', false),
            'orderRepository' => $this->orderRepository,
            'shipmentItemCollectionFactory' => $this->getMock(
                    'Magento\Sales\Model\ResourceModel\Order\Shipment\Item\CollectionFactory',
                    [],
                    [],
                    '',
                    false
                ),
            'trackCollectionFactory' => $this->getMock(
                    'Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory',
                    [],
                    [],
                    '',
                    false
                ),
            'commentFactory' => $this->getMock(
                    'Magento\Sales\Model\Order\Shipment\CommentFactory',
                    [],
                    [],
                    '',
                    false
                ),
            'commentCollectionFactory' => $this->getMock(
                    'Magento\Sales\Model\ResourceModel\Order\Shipment\Comment\CollectionFactory',
                    [],
                    [],
                    '',
                    false
                ),
        ];
        $this->shipment = $objectManagerHelper->getObject(
            'Magento\Sales\Model\Order\Shipment',
            $arguments
        );
    }

    public function testGetOrder()
    {
        $orderId = 100000041;
        $this->shipment->setOrderId($orderId);
        $entityName = 'shipment';
        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['load', 'setHistoryEntityName', '__wakeUp'],
            [],
            '',
            false
        );
        $this->shipment->setOrderId($orderId);
        $order->expects($this->atLeastOnce())
            ->method('setHistoryEntityName')
            ->with($entityName)
            ->will($this->returnSelf());

        $this->orderRepository->expects($this->atLeastOnce())
            ->method('get')
            ->will($this->returnValue($order));

        $this->assertEquals($order, $this->shipment->getOrder());
    }

    public function testGetEntityType()
    {
        $this->assertEquals('shipment', $this->shipment->getEntityType());
    }
}
