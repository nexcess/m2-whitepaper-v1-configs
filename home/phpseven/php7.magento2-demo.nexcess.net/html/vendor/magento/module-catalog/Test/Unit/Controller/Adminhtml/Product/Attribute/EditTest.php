<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Test\Unit\Controller\Adminhtml\Product\Attribute;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Attribute\Edit
     */
    protected $editController;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavAttribute;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPage;

    /**
     * @var  \Magento\Framework\View\Result\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultLayout;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageTitle;

    /**
     * @var \Magento\Backend\Block\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockTemplate;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->request = $this->getMockBuilder('Magento\Framework\App\RequestInterface')->getMock();

        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')->getMock();

        $this->eavAttribute = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Eav\Attribute',
            ['setEntityTypeId', 'load', 'getId', 'getEntityTypeId', 'addData', 'getName'],
            [],
            '',
            false
        );

        $this->registry = $this->getMock('Magento\Framework\Registry', [], [], '', false);

        $this->resultPage = $this->getMockBuilder('Magento\Backend\Model\View\Result\Page')
            ->disableOriginalConstructor()
            ->setMethods(['setActiveMenu', 'getConfig', 'addBreadcrumb', 'addHandle', 'getLayout'])
            ->getMock();

        $this->resultPageFactory = $this->getMockBuilder('Magento\Framework\View\Result\PageFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->resultLayout = $this->getMockBuilder('Magento\Framework\View\Result\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pageConfig = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pageTitle = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();

        $this->layout = $this->getMock('Magento\Framework\View\Layout', ['getBlock'], [], '', false);

        $this->session = $this->getMockBuilder('Magento\Backend\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockTemplate = $this->getMockBuilder('Magento\Backend\Block\Template')
            ->disableOriginalConstructor()
            ->getMock();

        $this->context = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $this->context->expects($this->any())->method('getRequest')->willReturn($this->request);
        $this->context->expects($this->any())->method('getObjectManager')->willReturn($this->objectManagerMock);
        $this->context->expects($this->any())->method('getResultPageFactory')->willReturn($this->resultPageFactory);
        $this->context->expects($this->any())->method('getSession')->willReturn($this->session);

        $this->objectManager = new ObjectManager($this);
        $this->editController = $this->objectManager->getObject(
            'Magento\Catalog\Controller\Adminhtml\Product\Attribute\Edit',
            [
                'context' => $this->context,
                'resultPageFactory' => $this->resultPageFactory
            ]
        );
    }

    public function testExecutePopup()
    {
        $attributesData = ['frontend_label' => ''];

        $this->request->expects($this->any())->method('getParam')->willReturnMap(
            [
                ['attribute_id', null, null],
                ['attribute', null, $attributesData],
                ['popup', null, '1'],
                ['product_tab', null, null]
            ]
        );

        $this->objectManagerMock->expects($this->any())->method('create')
            ->with('Magento\Catalog\Model\ResourceModel\Eav\Attribute')
            ->willReturn($this->eavAttribute);
        $this->objectManagerMock->expects($this->any())->method('get')
            ->with('Magento\Backend\Model\Session')
            ->willReturn($this->session);

        $this->eavAttribute->expects($this->once())->method('setEntityTypeId')->willReturnSelf();
        $this->eavAttribute->expects($this->once())->method('addData')->with($attributesData)->willReturnSelf();
        $this->eavAttribute->expects($this->any())->method('getName')->willReturn(null);

        $this->registry->expects($this->any())
            ->method('register')
            ->with('entity_attribute', $this->eavAttribute);

        $this->resultPage->expects($this->once())
            ->method('addHandle')
            ->with(['popup', 'catalog_product_attribute_edit_popup'])
            ->willReturnSelf();
        $this->resultPage->expects($this->any())->method('getConfig')->willReturn($this->pageConfig);
        $this->resultPage->expects($this->once())->method('getLayout')->willReturn($this->layout);

        $this->resultPageFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultPage);

        $this->pageConfig->expects($this->any())->method('addBodyClass')->willReturnSelf();
        $this->pageConfig->expects($this->any())->method('getTitle')->willReturn($this->pageTitle);

        $this->pageTitle->expects($this->any())->method('prepend')->willReturnSelf();

        $this->layout->expects($this->once())->method('getBlock')->willReturn($this->blockTemplate);

        $this->blockTemplate->expects($this->any())->method('setIsPopup')->willReturnSelf();

        $this->assertSame($this->resultPage, $this->editController->execute());
    }

    public function testExecuteNoPopup()
    {
        $attributesData = ['frontend_label' => ''];

        $this->request->expects($this->any())->method('getParam')->willReturnMap(
            [
                ['attribute_id', null, null],
                ['attribute', null, $attributesData],
                ['popup', null, false],
            ]
        );

        $this->objectManagerMock->expects($this->any())->method('create')
            ->with('Magento\Catalog\Model\ResourceModel\Eav\Attribute')
            ->willReturn($this->eavAttribute);
        $this->objectManagerMock->expects($this->any())->method('get')
            ->with('Magento\Backend\Model\Session')
            ->willReturn($this->session);

        $this->eavAttribute->expects($this->once())->method('setEntityTypeId')->willReturnSelf();
        $this->eavAttribute->expects($this->once())->method('addData')->with($attributesData)->willReturnSelf();

        $this->registry->expects($this->any())
            ->method('register')
            ->with('entity_attribute', $this->eavAttribute);

        $this->resultPage->expects($this->any())->method('addBreadcrumb')->willReturnSelf();
        $this->resultPage->expects($this->once())
            ->method('setActiveMenu')
            ->with('Magento_Catalog::catalog_attributes_attributes')
            ->willReturnSelf();
        $this->resultPage->expects($this->any())->method('getConfig')->willReturn($this->pageConfig);
        $this->resultPage->expects($this->once())->method('getLayout')->willReturn($this->layout);

        $this->resultPageFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultPage);

        $this->pageConfig->expects($this->any())->method('getTitle')->willReturn($this->pageTitle);

        $this->pageTitle->expects($this->any())->method('prepend')->willReturnSelf();

        $this->eavAttribute->expects($this->any())->method('getName')->willReturn(null);

        $this->layout->expects($this->once())->method('getBlock')->willReturn($this->blockTemplate);

        $this->blockTemplate->expects($this->any())->method('setIsPopup')->willReturnSelf();

        $this->assertSame($this->resultPage, $this->editController->execute());
    }
}
