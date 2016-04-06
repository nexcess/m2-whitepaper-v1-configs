<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Email\Test\Unit\Controller\Adminhtml\Email\Template;

/**
 * @covers \Magento\Email\Controller\Adminhtml\Email\Template\Index
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Email\Controller\Adminhtml\Email\Template\Index
     */
    protected $indexController;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Request|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\View|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Backend\Block\Menu|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $menuBlockMock;

    /**
     * @var \Magento\Backend\Block\Widget\Breadcrumbs|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $breadcrumbsBlockMock;

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

    protected function setUp()
    {
        $this->registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewMock = $this->getMockBuilder('Magento\Framework\App\View')
            ->disableOriginalConstructor()
            ->setMethods(['loadLayout', 'getLayout', 'getPage', 'renderLayout'])
            ->getMock();
        $this->layoutMock = $this->getMockBuilder('Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->setMethods(['getBlock'])
            ->getMock();
        $this->menuBlockMock = $this->getMockBuilder('\Magento\Backend\Block\Menu')
            ->disableOriginalConstructor()
            ->setMethods(['setActive', 'getMenuModel', 'getParentItems'])
            ->getMock();
        $this->breadcrumbsBlockMock = $this->getMockBuilder('\Magento\Backend\Block\Widget\Breadcrumbs')
            ->disableOriginalConstructor()
            ->setMethods(['addLink'])
            ->getMock();
        $this->resultPageMock = $this->getMockBuilder('Magento\Framework\View\Result\Page')
            ->disableOriginalConstructor()
            ->setMethods(['setActiveMenu', 'getConfig', 'addBreadcrumb'])
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTitleMock = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->context = $objectManager->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $this->requestMock,
                'view' => $this->viewMock
            ]
        );
        $this->indexController = $objectManager->getObject(
            'Magento\Email\Controller\Adminhtml\Email\Template\Index',
            [
                'context' => $this->context,
                'coreRegistry' => $this->registryMock
            ]
        );
    }

    /**
     * @covers \Magento\Email\Controller\Adminhtml\Email\Template\Index::execute
     */
    public function testExecute()
    {
        $this->prepareExecute();

        $this->viewMock->expects($this->atLeastOnce())
            ->method('getLayout')
            ->willReturn($this->layoutMock);
        $this->layoutMock->expects($this->at(0))
            ->method('getBlock')
            ->with('menu')
            ->will($this->returnValue($this->menuBlockMock));
        $this->menuBlockMock->expects($this->any())
            ->method('getMenuModel')
            ->will($this->returnSelf());
        $this->menuBlockMock->expects($this->any())
            ->method('getParentItems')
            ->will($this->returnValue([]));
        $this->viewMock->expects($this->once())
            ->method('getPage')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->pageConfigMock);
        $this->pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);
        $this->pageTitleMock->expects($this->once())
            ->method('prepend')
            ->with('Email Templates');
        $this->layoutMock->expects($this->at(1))
            ->method('getBlock')
            ->with('breadcrumbs')
            ->will($this->returnValue($this->breadcrumbsBlockMock));
        $this->breadcrumbsBlockMock->expects($this->any())
            ->method('addLink')
            ->willReturnSelf();

        $this->assertNull($this->indexController->execute());
    }

    /**
     * @covers \Magento\Email\Controller\Adminhtml\Email\Template\Index::execute
     */
    public function testExecuteAjax()
    {
        $this->prepareExecute(true);
        $indexController = $this->getMockBuilder('Magento\Email\Controller\Adminhtml\Email\Template\Index')
            ->setMethods(['getRequest', '_forward'])
            ->disableOriginalConstructor()
            ->getMock();
        $indexController->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->requestMock));
        $indexController->expects($this->once())
            ->method('_forward')
            ->with('grid');
        $this->assertNull($indexController->execute());
    }

    /**
     * @param bool $ajax
     */
    protected function prepareExecute($ajax = false)
    {
        $this->requestMock->expects($this->once())
            ->method('getQuery')
            ->with('ajax')
            ->willReturn($ajax);
    }
}
