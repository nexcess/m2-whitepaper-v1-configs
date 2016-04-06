<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Test\Unit\Controller\Adminhtml\Order\Invoice;

use Magento\Backend\App\Action;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class AddCommentTest
 * @package Magento\Sales\Controller\Adminhtml\Order\Invoice
 */
class AddCommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentSenderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

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
     * @var \Magento\Sales\Controller\Adminhtml\Order\Invoice\AddComment
     */
    protected $controller;

    /**
     * @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageFactoryMock;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultJsonFactoryMock;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRawFactoryMock;

    /**
     * @var \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultJsonMock;

    /**
     * SetUp method
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $titleMock = $this->getMockBuilder('Magento\Framework\App\Action\Title')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->responseMock = $this->getMockBuilder('Magento\Framework\App\Response\Http')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->viewMock = $this->getMockBuilder('Magento\Framework\App\View')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->resultPageMock = $this->getMockBuilder('Magento\Framework\View\Result\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTitleMock = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $this->getMockBuilder('Magento\Backend\App\Action\Context')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $contextMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->requestMock));
        $contextMock->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($this->responseMock));
        $contextMock->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue($titleMock));
        $contextMock->expects($this->any())
            ->method('getView')
            ->will($this->returnValue($this->viewMock));
        $contextMock->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManagerMock));
        $this->viewMock->expects($this->any())
            ->method('getPage')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->pageConfigMock);
        $this->pageConfigMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);

        $this->resultPageFactoryMock = $this->getMockBuilder('Magento\Framework\View\Result\PageFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->resultJsonMock = $this->getMockBuilder('Magento\Framework\Controller\Result\Json')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->resultRawFactoryMock = $this->getMockBuilder('Magento\Framework\Controller\Result\RawFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->resultJsonFactoryMock = $this->getMockBuilder('Magento\Framework\Controller\Result\JsonFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->commentSenderMock = $this->getMockBuilder('Magento\Sales\Model\Order\Email\Sender\InvoiceCommentSender')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->controller = $objectManager->getObject(
            'Magento\Sales\Controller\Adminhtml\Order\Invoice\AddComment',
            [
                'context' => $contextMock,
                'invoiceCommentSender' => $this->commentSenderMock,
                'resultPageFactory' => $this->resultPageFactoryMock,
                'resultRawFactory' => $this->resultRawFactoryMock,
                'resultJsonFactory' => $this->resultJsonFactoryMock
            ]
        );
    }

    /**
     * Test execute
     *
     * @return void
     */
    public function testExecute()
    {
        $data = ['comment' => 'test comment'];
        $invoiceId = 2;
        $response = 'some result';

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('id')
            ->willReturn($invoiceId);
        $this->requestMock->expects($this->at(1))
            ->method('setParam');
        $this->requestMock->expects($this->at(2))
            ->method('getPost')
            ->with('comment')
            ->willReturn($data);
        $this->requestMock->expects($this->at(3))
            ->method('getParam')
            ->with('invoice_id')
            ->willReturn($invoiceId);

        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $invoiceMock->expects($this->once())
            ->method('addComment')
            ->with($data['comment'], false, false);
        $invoiceMock->expects($this->once())
            ->method('save');

        $invoiceRepository = $this->getMockBuilder('Magento\Sales\Api\InvoiceRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $invoiceRepository->expects($this->any())
            ->method('get')
            ->willReturn($invoiceMock);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Sales\Api\InvoiceRepositoryInterface')
            ->willReturn($invoiceRepository);

        $commentsBlockMock = $this->getMockBuilder('Magento\Sales\Block\Adminhtml\Order\Invoice\View\Comments')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $commentsBlockMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($response));

        $layoutMock = $this->getMockBuilder('Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('invoice_comments')
            ->will($this->returnValue($commentsBlockMock));

        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->resultPageMock));

        $this->resultPageMock->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $this->commentSenderMock->expects($this->once())
            ->method('send')
            ->with($invoiceMock, false, $data['comment']);

        $resultRaw = $this->getMockBuilder('Magento\Framework\Controller\Result\Raw')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $resultRaw->expects($this->once())->method('setContents')->with($response);

        $this->resultRawFactoryMock->expects($this->once())->method('create')->will($this->returnValue($resultRaw));
        $this->assertSame($resultRaw, $this->controller->execute());
    }

    /**
     * Test execute model exception
     *
     * @return void
     */
    public function testExecuteModelException()
    {
        $message = 'model exception';
        $response = ['error' => true, 'message' => $message];
        $e = new \Magento\Framework\Exception\LocalizedException(__($message));

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->will($this->throwException($e));

        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->resultJsonMock));

        $this->resultJsonMock->expects($this->once())->method('setData')->with($response);
        $this->assertSame($this->resultJsonMock, $this->controller->execute());
    }

    /**
     * Test execute exception
     *
     * @return void
     */
    public function testExecuteException()
    {
        $response = ['error' => true, 'message' => 'Cannot add new comment.'];
        $e = new \Exception('test');

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->will($this->throwException($e));

        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->resultJsonMock));

        $this->resultJsonMock->expects($this->once())->method('setData')->with($response);
        $this->assertSame($this->resultJsonMock, $this->controller->execute());
    }
}
