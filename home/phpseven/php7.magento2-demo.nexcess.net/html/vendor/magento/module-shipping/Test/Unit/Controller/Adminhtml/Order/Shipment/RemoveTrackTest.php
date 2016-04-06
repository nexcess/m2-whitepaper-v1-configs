<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Shipping\Test\Unit\Controller\Adminhtml\Order\Shipment;

/**
 * Class RemoveTrackTest
 */
class RemoveTrackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentLoaderMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\Track|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentTrackMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentMock;

    /**
     * @var \Magento\Framework\App\View|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\Framework\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfigMock;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageTitleMock;

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\Shipment\RemoveTrack
     */
    protected $controller;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', ['getParam'], [], '', false);
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->shipmentTrackMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment\Track',
            ['load', 'getId', 'delete', '__wakeup'],
            [],
            '',
            false
        );
        $this->shipmentMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment',
            ['getIncrementId', '__wakeup'],
            [],
            '',
            false
        );
        $this->viewMock = $this->getMock(
            'Magento\Framework\App\View',
            ['loadLayout', 'getLayout', 'getPage'],
            [],
            '',
            false
        );
        $this->responseMock = $this->getMock(
            'Magento\Framework\App\Response\Http',
            [],
            [],
            '',
            false
        );
        $this->shipmentLoaderMock = $this->getMock(
            'Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader',
            ['setOrderId', 'setShipmentId', 'setShipment', 'setTracking', 'load'],
            [],
            '',
            false
        );
        $this->resultPageMock = $this->getMockBuilder('Magento\Framework\View\Result\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTitleMock = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock = $this->getMock(
            'Magento\Backend\App\Action\Context',
            ['getRequest', 'getObjectManager', 'getTitle', 'getView', 'getResponse'],
            [],
            '',
            false
        );

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Sales\Model\Order\Shipment\Track')
            ->will($this->returnValue($this->shipmentTrackMock));

        $contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($this->requestMock));
        $contextMock->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManagerMock));
        $contextMock->expects($this->any())->method('getView')->will($this->returnValue($this->viewMock));
        $contextMock->expects($this->any())->method('getResponse')->will($this->returnValue($this->responseMock));

        $this->controller = new \Magento\Shipping\Controller\Adminhtml\Order\Shipment\RemoveTrack(
            $contextMock,
            $this->shipmentLoaderMock
        );

        $this->viewMock->expects($this->any())
            ->method('getPage')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->pageConfigMock);
        $this->pageConfigMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);
    }

    /**
     * Shipment load sections
     *
     * @return void
     */
    protected function shipmentLoad()
    {
        $orderId = 1;
        $shipmentId = 1;
        $trackId = 1;
        $shipment = [];
        $tracking = [];

        $this->shipmentTrackMock->expects($this->once())
            ->method('load')
            ->with($trackId)
            ->will($this->returnSelf());
        $this->shipmentTrackMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($trackId));
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('track_id')
            ->will($this->returnValue($trackId));
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('order_id')
            ->will($this->returnValue($orderId));
        $this->requestMock->expects($this->at(2))
            ->method('getParam')
            ->with('shipment_id')
            ->will($this->returnValue($shipmentId));
        $this->requestMock->expects($this->at(3))
            ->method('getParam')
            ->with('shipment')
            ->will($this->returnValue($shipment));
        $this->requestMock->expects($this->at(4))
            ->method('getParam')
            ->with('tracking')
            ->will($this->returnValue($tracking));
        $this->shipmentLoaderMock->expects($this->once())->method('setOrderId')->with($orderId);
        $this->shipmentLoaderMock->expects($this->once())->method('setShipmentId')->with($shipmentId);
        $this->shipmentLoaderMock->expects($this->once())->method('setShipment')->with($shipment);
        $this->shipmentLoaderMock->expects($this->once())->method('setTracking')->with($tracking);
    }

    /**
     * Represent json json section
     *
     * @param array $errors
     * @return void
     */
    protected function representJson(array $errors)
    {
        $jsonHelper = $this->getMock('Magento\Framework\Json\Helper\Data', ['jsonEncode'], [], '', false);
        $jsonHelper->expects($this->once())
            ->method('jsonEncode')
            ->with($errors)
            ->will($this->returnValue('{json}'));
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\Json\Helper\Data')
            ->will($this->returnValue($jsonHelper));
        $this->responseMock->expects($this->once())
            ->method('representJson')
            ->with('{json}');
    }

    /**
     * Run test execute method
     */
    public function testExecute()
    {
        $response = 'html-data';
        $this->shipmentLoad();

        $this->shipmentLoaderMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue($this->shipmentMock));
        $this->shipmentTrackMock->expects($this->once())
            ->method('delete')
            ->will($this->returnSelf());

        $layoutMock = $this->getMock('Magento\Framework\View\Layout', ['getBlock'], [], '', false);
        $trackingBlockMock = $this->getMock(
            'Magento\Shipping\Block\Adminhtml\Order\Tracking',
            ['toHtml'],
            [],
            '',
            false
        );

        $trackingBlockMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($response));
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('shipment_tracking')
            ->will($this->returnValue($trackingBlockMock));
        $this->viewMock->expects($this->once())->method('loadLayout')->will($this->returnSelf());
        $this->viewMock->expects($this->any())->method('getLayout')->will($this->returnValue($layoutMock));
        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with($response);

        $this->assertNull($this->controller->execute());
    }

    /**
     * Run test execute method (fail track load)
     */
    public function testExecuteTrackIdFail()
    {
        $trackId = null;
        $errors = ['error' => true, 'message' => 'We can\'t load track with retrieving identifier right now.'];

        $this->shipmentTrackMock->expects($this->once())
            ->method('load')
            ->with($trackId)
            ->will($this->returnSelf());
        $this->shipmentTrackMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($trackId));
        $this->representJson($errors);

        $this->assertNull($this->controller->execute());
    }

    /**
     * Run test execute method (fail load shipment)
     */
    public function testExecuteShipmentLoadFail()
    {
        $errors = [
            'error' => true,
            'message' => 'We can\'t initialize shipment for delete tracking number.',
        ];
        $this->shipmentLoad();

        $this->shipmentLoaderMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue(null));
        $this->representJson($errors);

        $this->assertNull($this->controller->execute());
    }

    /**
     * Run test execute method (delete exception)
     */
    public function testExecuteDeleteFail()
    {
        $errors = ['error' => true, 'message' => 'We can\'t delete tracking number.'];
        $this->shipmentLoad();

        $this->shipmentLoaderMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue($this->shipmentMock));
        $this->shipmentTrackMock->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new \Exception()));
        $this->representJson($errors);

        $this->assertNull($this->controller->execute());
    }
}
