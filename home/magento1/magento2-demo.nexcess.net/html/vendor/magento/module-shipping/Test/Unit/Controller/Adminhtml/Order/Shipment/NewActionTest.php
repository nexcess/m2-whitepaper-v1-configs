<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Shipping\Test\Unit\Controller\Adminhtml\Order\Shipment;

use Magento\Backend\App\Action;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class NewActionTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NewActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentLoader;

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\Shipment\NewAction
     */
    protected $newAction;

    /**
     * @var Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\Framework\Message\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\ActionFlag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionFlag;

    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helper;

    /**
     * @var  \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setUp()
    {
        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->shipmentLoader = $this->getMockBuilder('Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->labelGenerator = $this->getMockBuilder('Magento\Shipping\Model\Shipping\LabelGenerator')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->shipmentSender = $this->getMockBuilder('Magento\Sales\Model\Order\Email\Sender\ShipmentSender')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->context = $this->getMockBuilder('Magento\Backend\App\Action\Context')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->context = $this->getMock(
            'Magento\Backend\App\Action\Context',
            [
                'getRequest', 'getResponse', 'getMessageManager', 'getRedirect', 'getObjectManager',
                'getSession', 'getActionFlag', 'getHelper', 'getView'
            ],
            [],
            '',
            false
        );
        $this->response = $this->getMock(
            'Magento\Framework\App\ResponseInterface',
            ['setRedirect', 'sendResponse'],
            [],
            '',
            false
        );
        $this->request = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()->getMock();
        $this->messageManager = $this->getMock(
            'Magento\Framework\Message\Manager',
            ['addSuccess', 'addError'],
            [],
            '',
            false
        );
        $this->session = $this->getMock(
            'Magento\Backend\Model\Session',
            ['setIsUrlNotice', 'getCommentText'],
            [],
            '',
            false
        );
        $this->actionFlag = $this->getMock('Magento\Framework\App\ActionFlag', ['get'], [], '', false);
        $this->helper = $this->getMock('Magento\Backend\Helper\Data', ['getUrl'], [], '', false);
        $this->view = $this->getMock('Magento\Framework\App\ViewInterface', [], [], '', false);
        $this->resultPageMock = $this->getMockBuilder('Magento\Framework\View\Result\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTitleMock = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->expects($this->once())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManager));
        $this->context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->response));
        $this->context->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $this->context->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($this->session));
        $this->context->expects($this->once())
            ->method('getActionFlag')
            ->will($this->returnValue($this->actionFlag));
        $this->context->expects($this->once())
            ->method('getHelper')
            ->will($this->returnValue($this->helper));
        $this->context->expects($this->once())->method('getView')->will($this->returnValue($this->view));
        $this->newAction = $objectManagerHelper->getObject(
            'Magento\Shipping\Controller\Adminhtml\Order\Shipment\NewAction',
            [
                'context' => $this->context, 'shipmentLoader' => $this->shipmentLoader, 'request' => $this->request,
                'response' => $this->response, 'view' => $this->view
            ]
        );
    }

    public function testExecute()
    {
        $shipmentId = 1000012;
        $orderId = 10003;
        $tracking = [];
        $shipmentData = ['items' => [], 'send_email' => ''];
        $shipment = $this->getMock(
            'Magento\Sales\Model\Order\Shipment',
            ['load', 'save', 'register', 'getOrder', 'getOrderId', '__wakeup'],
            [],
            '',
            false
        );
        $this->request->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['order_id', null, $orderId],
                        ['shipment_id', null, $shipmentId],
                        ['shipment', null, $shipmentData],
                        ['tracking', null, $tracking],
                    ]
                )
            );
        $this->shipmentLoader->expects($this->any())
            ->method('setShipmentId')
            ->with($shipmentId);
        $this->shipmentLoader->expects($this->any())
            ->method('setOrderId')
            ->with($orderId);
        $this->shipmentLoader->expects($this->any())
            ->method('setShipment')
            ->with($shipmentData);
        $this->shipmentLoader->expects($this->any())
            ->method('setTracking')
            ->with($tracking);
        $this->shipmentLoader->expects($this->once())
            ->method('load')
            ->will($this->returnValue($shipment));
        $this->session->expects($this->once())
            ->method('getCommentText')
            ->with(true)
            ->will($this->returnValue(''));
        $this->objectManager->expects($this->atLeastOnce())
            ->method('get')
            ->with('Magento\Backend\Model\Session')
            ->will($this->returnValue($this->session));
        $this->view->expects($this->once())
            ->method('loadLayout')
            ->will($this->returnSelf());
        $this->view->expects($this->once())
            ->method('renderLayout')
            ->will($this->returnSelf());
        $this->view->expects($this->any())
            ->method('getPage')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->pageConfigMock);
        $this->pageConfigMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);
        $layout = $this->getMock('Magento\Framework\View\Layout\Element\Layout', ['getBlock'], [], '', false);
        $menuBlock = $this->getMock(
            'Magento\Framework\View\Element\BlockInterface',
            ['toHtml', 'setActive', 'getMenuModel'],
            [],
            '',
            false
        );
        $menuModel = $this->getMockBuilder('Magento\Backend\Model\Menu')
            ->disableOriginalConstructor()->getMock();
        $itemId = 'Magento_Sales::sales_order';
        $parents = [
            new \Magento\Framework\DataObject(['title' => 'title1']),
            new \Magento\Framework\DataObject(['title' => 'title2']),
            new \Magento\Framework\DataObject(['title' => 'title3']),
        ];
        $menuModel->expects($this->once())
            ->method('getParentItems')
            ->with($itemId)
            ->will($this->returnValue($parents));
        $menuBlock->expects($this->once())
            ->method('setActive')
            ->with($itemId);
        $menuBlock->expects($this->once())
            ->method('getMenuModel')
            ->will($this->returnValue($menuModel));
        $this->view->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layout));
        $layout->expects($this->once())
            ->method('getBlock')
            ->with('menu')
            ->will($this->returnValue($menuBlock));

        $this->assertNull($this->newAction->execute());
    }
}
