<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Test\Unit\Controller\Adminhtml\Order;

/**
 * @covers \Magento\Sales\Controller\Adminhtml\Order\View
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\View
     */
    protected $viewAction;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Framework\App\ActionFlag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionFlagMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistryMock;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfigMock;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageTitleMock;

    /**
     * @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderManagementMock;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderRepositoryMock;

    public function setUp()
    {
        $this->orderManagementMock = $this->getMockBuilder('Magento\Sales\Api\OrderManagementInterface')
            ->getMockForAbstractClass();
        $this->orderRepositoryMock = $this->getMockBuilder('Magento\Sales\Api\OrderRepositoryInterface')
            ->getMockForAbstractClass();
        $this->loggerMock = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->getMockForAbstractClass();
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\RequestInterface')
            ->getMock();
        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->getMock();
        $this->orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManagerMock = $this->getMockBuilder('Magento\Framework\Message\ManagerInterface')
            ->getMock();
        $this->actionFlagMock = $this->getMockBuilder('Magento\Framework\App\ActionFlag')
            ->disableOriginalConstructor()
            ->getMock();
        $this->coreRegistryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTitleMock = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageFactoryMock = $this->getMockBuilder('Magento\Framework\View\Result\PageFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->resultRedirectFactoryMock = $this->getMockBuilder('Magento\Backend\Model\View\Result\RedirectFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->resultPageMock = $this->getMockBuilder('Magento\Backend\Model\View\Result\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectMock = $this->getMockBuilder('Magento\Backend\Model\View\Result\Redirect')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->context = $objectManager->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $this->requestMock,
                'objectManager' => $this->objectManagerMock,
                'actionFlag' => $this->actionFlagMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock
            ]
        );
        $this->viewAction = $objectManager->getObject(
            'Magento\Sales\Controller\Adminhtml\Order\View',
            [
                'context' => $this->context,
                'coreRegistry' => $this->coreRegistryMock,
                'resultPageFactory' => $this->resultPageFactoryMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
                'orderManagement' => $this->orderManagementMock,
                'orderRepository' => $this->orderRepositoryMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * @covers \Magento\Sales\Controller\Adminhtml\Order\View::execute
     */
    public function testExecute()
    {
        $id = 111;
        $titlePart = '#111';
        $this->initOrder();
        $this->initOrderSuccess($id);
        $this->prepareRedirect();
        $this->initAction();

        $this->resultPageMock->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn($this->pageConfigMock);
        $this->pageConfigMock->expects($this->atLeastOnce())
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);
        $this->orderMock->expects($this->atLeastOnce())
            ->method('getIncrementId')
            ->willReturn($id);
        $this->pageTitleMock->expects($this->exactly(2))
            ->method('prepend')
            ->withConsecutive(
                ['Orders'],
                [$titlePart]
            )
            ->willReturnSelf();

        $this->assertInstanceOf(
            'Magento\Backend\Model\View\Result\Page',
            $this->viewAction->execute()
        );
    }

    /**
     * @covers \Magento\Sales\Controller\Adminhtml\Order\View::execute
     */
    public function testExecuteNoOrder()
    {
        $orderIdParam = 111;

        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('order_id')
            ->willReturn($orderIdParam);
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderIdParam)
            ->willThrowException(
                new \Magento\Framework\Exception\NoSuchEntityException(__('Requested entity doesn\'t exist'))
            );
        $this->initOrderFail();
        $this->prepareRedirect();
        $this->setPath('sales/*/');

        $this->assertInstanceOf(
            'Magento\Backend\Model\View\Result\Redirect',
            $this->viewAction->execute()
        );
    }

    /**
     * @covers \Magento\Sales\Controller\Adminhtml\Order\View::execute
     */
    public function testGlobalException()
    {
        $id = 111;
        $exception = new \Exception();
        $this->initOrder();
        $this->initOrderSuccess($id);
        $this->prepareRedirect();

        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willThrowException($exception);
        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with($exception);
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('Exception occurred during order load')
            ->willReturnSelf();
        $this->setPath('sales/order/index');

        $this->assertInstanceOf(
            'Magento\Backend\Model\View\Result\Redirect',
            $this->viewAction->execute()
        );
    }

    protected function initOrder()
    {
        $orderIdParam = 111;

        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('order_id')
            ->willReturn($orderIdParam);
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderIdParam)
            ->willReturn($this->orderMock);
    }

    /**
     * init Order Success
     */
    protected function initOrderSuccess()
    {
        $this->coreRegistryMock->expects($this->exactly(2))
            ->method('register')
            ->withConsecutive(
                ['sales_order', $this->orderMock],
                ['current_order', $this->orderMock]
            );
    }

    protected function initOrderFail()
    {
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('This order no longer exists.')
            ->willReturnSelf();
        $this->actionFlagMock->expects($this->once())
            ->method('set')
            ->with('', \Magento\Sales\Controller\Adminhtml\Order::FLAG_NO_DISPATCH, true);
    }

    protected function initAction()
    {
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->with('Magento_Sales::sales_order')
            ->willReturnSelf();
        $this->resultPageMock->expects($this->exactly(2))
            ->method('addBreadcrumb')
            ->withConsecutive(
                ['Sales', 'Sales'],
                ['Orders', 'Orders']
            )
            ->willReturnSelf();
    }

    protected function prepareRedirect()
    {
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultRedirectMock);
    }

    /**
     * @param string $path
     * @param array $params
     */
    protected function setPath($path, $params = [])
    {
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with($path, $params);
    }
}
