<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cms\Test\Unit\Controller\Adminhtml\Page;

use Magento\Cms\Controller\Adminhtml\Page\InlineEdit;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEditTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $messageManager;

    /** @var \Magento\Framework\Message\MessageInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $message;

    /** @var \Magento\Framework\Message\Collection|\PHPUnit_Framework_MockObject_MockObject */
    protected $messageCollection;

    /** @var \Magento\Cms\Model\Page|\PHPUnit_Framework_MockObject_MockObject */
    protected $cmsPage;

    /** @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor|\PHPUnit_Framework_MockObject_MockObject */
    protected $dataProcessor;

    /** @var \Magento\Cms\Api\PageRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $pageRepository;

    /** @var \Magento\Framework\Controller\Result\JsonFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $jsonFactory;

    /** @var \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject */
    protected $resultJson;

    /** @var InlineEdit */
    protected $controller;

    public function setUp()
    {
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $this->messageManager = $this->getMockForAbstractClass('Magento\Framework\Message\ManagerInterface');
        $this->messageCollection = $this->getMock('Magento\Framework\Message\Collection', [], [], '', false);
        $this->message = $this->getMockForAbstractClass('Magento\Framework\Message\MessageInterface');
        $this->cmsPage = $this->getMock('Magento\Cms\Model\Page', [], [], '', false);
        $this->context = $helper->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $this->request,
                'messageManager' => $this->messageManager
            ]
        );
        $this->dataProcessor = $this->getMock(
            'Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor',
            [],
            [],
            '',
            false
        );
        $this->pageRepository = $this->getMockForAbstractClass('Magento\Cms\Api\PageRepositoryInterface');
        $this->resultJson = $this->getMock('Magento\Framework\Controller\Result\Json', [], [], '', false);
        $this->jsonFactory = $this->getMock(
            'Magento\Framework\Controller\Result\JsonFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->controller = new InlineEdit(
            $this->context,
            $this->dataProcessor,
            $this->pageRepository,
            $this->jsonFactory
        );
    }

    public function prepareMocksForTestExecute()
    {
        $postData = [
            1 => [
                'title' => '404 Not Found',
                'identifier' => 'no-route',
                'custom_theme' => '1',
                'custom_root_template' => '2'
            ]
        ];
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['isAjax', null, true],
                    ['items', [], $postData]
                ]
            );
        $this->pageRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($this->cmsPage);
        $this->dataProcessor->expects($this->once())
            ->method('filter')
            ->with($postData[1])
            ->willReturnArgument(0);
        $this->dataProcessor->expects($this->once())
            ->method('validate')
            ->with($postData[1])
            ->willReturn(false);
        $this->messageManager->expects($this->once())
            ->method('getMessages')
            ->with(true)
            ->willReturn($this->messageCollection);
        $this->messageCollection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->message]);
        $this->message->expects($this->once())
            ->method('getText')
            ->willReturn('Error message');
        $this->cmsPage->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('1');
        $this->cmsPage->expects($this->atLeastOnce())
            ->method('getData')
            ->willReturn([
                'layout' => '1column',
                'identifier' => 'test-identifier'
            ]);
        $this->cmsPage->expects($this->once())
            ->method('setData')
            ->with([
                'layout' => '1column',
                'title' => '404 Not Found',
                'identifier' => 'no-route',
                'custom_theme' => '1',
                'custom_root_template' => '2'
            ]);
        $this->jsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->resultJson);
    }

    public function testExecuteWithLocalizedException()
    {
        $this->prepareMocksForTestExecute();
        $this->pageRepository->expects($this->once())
            ->method('save')
            ->with($this->cmsPage)
            ->willThrowException(new \Magento\Framework\Exception\LocalizedException(__('LocalizedException')));
        $this->resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => [
                    '[Page ID: 1] Error message',
                    '[Page ID: 1] LocalizedException'
                ],
                'error' => true
            ])
            ->willReturnSelf();

        $this->assertSame($this->resultJson, $this->controller->execute());
    }

    public function testExecuteWithRuntimeException()
    {
        $this->prepareMocksForTestExecute();
        $this->pageRepository->expects($this->once())
            ->method('save')
            ->with($this->cmsPage)
            ->willThrowException(new \RuntimeException(__('RuntimeException')));
        $this->resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => [
                    '[Page ID: 1] Error message',
                    '[Page ID: 1] RuntimeException'
                ],
                'error' => true
            ])
            ->willReturnSelf();

        $this->assertSame($this->resultJson, $this->controller->execute());
    }

    public function testExecuteWithException()
    {
        $this->prepareMocksForTestExecute();
        $this->pageRepository->expects($this->once())
            ->method('save')
            ->with($this->cmsPage)
            ->willThrowException(new \Exception(__('Exception')));
        $this->resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => [
                    '[Page ID: 1] Error message',
                    '[Page ID: 1] Something went wrong while saving the page.'
                ],
                'error' => true
            ])
            ->willReturnSelf();

        $this->assertSame($this->resultJson, $this->controller->execute());
    }

    public function testExecuteWithoutData()
    {
        $this->jsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->resultJson);
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['items', [], []],
                    ['isAjax', null, true]
                ]
            );
        $this->resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => [
                    'Please correct the data sent.'
                ],
                'error' => true
            ])
            ->willReturnSelf();

        $this->assertSame($this->resultJson, $this->controller->execute());
    }

    public function testSetCmsPageData()
    {
        $extendedPageData = [
            'page_id' => '2',
            'title' => 'Home Page',
            'page_layout' => '1column',
            'identifier' => 'home',
            'content_heading' => 'Home Page',
            'content' => 'CMS homepage content goes here.',
            'is_active' => '1',
            'sort_order' => '1',
            'custom_theme' => '3',
            'website_root' => '1',
            'under_version_control' => '0',
            'store_id' => ['0']
        ];
        $pageData = [
            'page_id' => '2',
            'title' => 'Home Page',
            'page_layout' => '1column',
            'identifier' => 'home',
            'is_active' => '1',
            'custom_theme' => '3',
            'under_version_control' => '0',
        ];
        $getData = [
            'page_id' => '2',
            'title' => 'Home Page',
            'page_layout' => '1column',
            'identifier' => 'home',
            'content_heading' => 'Home Page',
            'content' => 'CMS homepage content goes here.',
            'is_active' => '1',
            'sort_order' => '1',
            'custom_theme' => '3',
            'custom_root_template' => '1column',
            'published_revision_id' => '0',
            'website_root' => '1',
            'under_version_control' => '0',
            'store_id' => ['0']
        ];
        $mergedData = [
            'page_id' => '2',
            'title' => 'Home Page',
            'page_layout' => '1column',
            'identifier' => 'home',
            'content_heading' => 'Home Page',
            'content' => 'CMS homepage content goes here.',
            'is_active' => '1',
            'sort_order' => '1',
            'custom_theme' => '3',
            'custom_root_template' => '1column',
            'published_revision_id' => '0',
            'website_root' => '1',
            'under_version_control' => '0',
            'store_id' => ['0']
        ];
        $this->cmsPage->expects($this->once())->method('getData')->willReturn($getData);
        $this->cmsPage->expects($this->once())->method('setData')->with($mergedData)->willReturnSelf();
        $this->assertSame(
            $this->controller,
            $this->controller->setCmsPageData($this->cmsPage, $extendedPageData, $pageData)
        );
    }
}
