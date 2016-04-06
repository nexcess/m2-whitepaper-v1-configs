<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Test\Unit\Controller\Adminhtml\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class MassCancelTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassCancelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\MassCancel
     */
    protected $massAction;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\Message\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\Framework\App\ActionFlag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionFlagMock;

    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionFactoryMock;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    public function setUp()
    {
        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->contextMock = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\Manager', [], [], '', false);
        $this->responseMock = $this->getMock('Magento\Framework\App\ResponseInterface', [], [], '', false);
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->objectManagerMock = $this->getMock(
            'Magento\Framework\ObjectManager\ObjectManager',
            [],
            [],
            '',
            false
        );

        $resultRedirectFactory = $this->getMock(
            'Magento\Backend\Model\View\Result\RedirectFactory',
            [],
            [],
            '',
            false
        );

        $this->orderCollectionMock = $this->getMockBuilder('Magento\Sales\Model\ResourceModel\Order\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $resourceCollection = 'Magento\Sales\Model\ResourceModel\Order\CollectionFactory';
        $this->orderCollectionFactoryMock = $this->getMockBuilder($resourceCollection)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->sessionMock = $this->getMock('Magento\Backend\Model\Session', ['setIsUrlNotice'], [], '', false);
        $this->actionFlagMock = $this->getMock('Magento\Framework\App\ActionFlag', ['get', 'set'], [], '', false);
        $this->helperMock = $this->getMock('\Magento\Backend\Helper\Data', ['getUrl'], [], '', false);
        $this->resultRedirectMock = $this->getMock('Magento\Backend\Model\View\Result\Redirect', [], [], '', false);
        $resultRedirectFactory->expects($this->any())->method('create')->willReturn($this->resultRedirectMock);

        $redirectMock = $this->getMockBuilder('Magento\Backend\Model\View\Result\Redirect')
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactoryMock = $this->getMockBuilder('Magento\Framework\Controller\ResultFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)
            ->willReturn($redirectMock);

        $this->contextMock->expects($this->once())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->once())->method('getResponse')->willReturn($this->responseMock);
        $this->contextMock->expects($this->once())->method('getObjectManager')->willReturn($this->objectManagerMock);
        $this->contextMock->expects($this->once())->method('getSession')->willReturn($this->sessionMock);
        $this->contextMock->expects($this->once())->method('getActionFlag')->willReturn($this->actionFlagMock);
        $this->contextMock->expects($this->once())->method('getHelper')->willReturn($this->helperMock);
        $this->contextMock->expects($this->once())
            ->method('getResultRedirectFactory')
            ->willReturn($resultRedirectFactory);
        $this->contextMock->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($resultFactoryMock);
        $this->filterMock = $this->getMock('Magento\Ui\Component\MassAction\Filter', [], [], '', false);
        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($this->orderCollectionMock)
            ->willReturn($this->orderCollectionMock);
        $this->orderCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->orderCollectionMock);
        $this->massAction = $objectManagerHelper->getObject(
            'Magento\Sales\Controller\Adminhtml\Order\MassCancel',
            [
                'context' => $this->contextMock,
                'filter' => $this->filterMock,
                'collectionFactory' => $this->orderCollectionFactoryMock
            ]
        );
    }

    /**
     * Test for selected orders
     * Two orders, only $order1 can be canceled
     */
    public function testExecuteCanCancelOneOrder()
    {
        $order1 = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();
        $order2 = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();
        $orders = [$order1, $order2];
        $countOrders = count($orders);

        $this->orderCollectionMock->expects($this->any())
            ->method('getItems')
            ->willReturn($orders);

        $order1->expects($this->once())
            ->method('canCancel')
            ->willReturn(true);
        $order1->expects($this->once())
            ->method('cancel');
        $order1->expects($this->once())
            ->method('save');

        $this->orderCollectionMock->expects($this->once())
            ->method('count')
            ->willReturn($countOrders);

        $order2->expects($this->once())
            ->method('canCancel')
            ->willReturn(false);

        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('1 order(s) cannot be canceled.');

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with('We canceled 1 order(s).');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('sales/*/')
            ->willReturnSelf();

        $this->massAction->execute();
    }

    /**
     * Test for excluded orders
     * Two orders could't be canceled
     */
    public function testExcludedCannotCancelOrders()
    {
        $order1 = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();
        $order2 = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $orders = [$order1, $order2];
        $countOrders = count($orders);

        $this->orderCollectionMock->expects($this->any())
            ->method('getItems')
            ->willReturn([$order1, $order2]);

        $order1->expects($this->once())
            ->method('canCancel')
            ->willReturn(false);

        $this->orderCollectionMock->expects($this->once())
            ->method('count')
            ->willReturn($countOrders);

        $order2->expects($this->once())
            ->method('canCancel')
            ->willReturn(false);

        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('You cannot cancel the order(s).');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('sales/*/')
            ->willReturnSelf();

        $this->massAction->execute();
    }

    /**
     * Order throws exception while canceling
     */
    public function testException()
    {
        $exception = new \Exception('Can not cancel');

        $order1 = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderCollectionMock->expects($this->any())
            ->method('getItems')
            ->willReturn([$order1]);

        $order1->expects($this->once())
            ->method('canCancel')
            ->willReturn(true);
        $order1->expects($this->once())
            ->method('cancel')
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('Can not cancel');

        $this->massAction->execute();
    }
}
