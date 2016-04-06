<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Newsletter\Test\Unit\Block\Adminhtml\Template;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class PreviewTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Newsletter\Block\Adminhtml\Template\Preview */
    protected $preview;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Newsletter\Model\Template|\PHPUnit_Framework_MockObject_MockObject */
    protected $template;

    /** @var \Magento\Newsletter\Model\SubscriberFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $subscriberFactory;

    /** @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject */
    protected $appState;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    protected function setUp()
    {
        $this->request = $this->getMock('Magento\Framework\App\RequestInterface', [], [], '', false);
        $this->appState = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface', [], [], '', false);
        $this->template = $this->getMock(
            'Magento\Newsletter\Model\Template',
            [
                'setTemplateType',
                'setTemplateText',
                'setTemplateStyles',
                'isPlain',
                'emulateDesign',
                'revertDesign',
                'getProcessedTemplate',
                'load'
            ],
            [],
            '',
            false
        );
        $templateFactory = $this->getMock('Magento\Newsletter\Model\TemplateFactory', ['create'], [], '', false);
        $templateFactory->expects($this->once())->method('create')->willReturn($this->template);
        $this->subscriberFactory = $this->getMock(
            'Magento\Newsletter\Model\SubscriberFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->preview = $this->objectManagerHelper->getObject(
            'Magento\Newsletter\Block\Adminhtml\Template\Preview',
            [
                'appState' => $this->appState,
                'storeManager' => $this->storeManager,
                'request' => $this->request,
                'templateFactory' => $templateFactory,
                'subscriberFactory' => $this->subscriberFactory
            ]
        );
    }

    public function testToHtml()
    {
        $this->request->expects($this->any())->method('getParam')->willReturnMap(
            [
                ['id', null, 1],
                ['store', null, 1]
            ]
        );

        $this->template->expects($this->atLeastOnce())->method('emulateDesign')->with(1);
        $this->template->expects($this->atLeastOnce())->method('revertDesign');

        $this->appState->expects($this->atLeastOnce())->method('emulateAreaCode')
            ->with(
                \Magento\Newsletter\Model\Template::DEFAULT_DESIGN_AREA,
                [$this->template, 'getProcessedTemplate'],
                [['subscriber' => null]]
            )
            ->willReturn('Processed Template');

        $this->assertEquals('Processed Template', $this->preview->toHtml());
    }

    public function testToHtmlForNewTemplate()
    {
        $this->request->expects($this->any())->method('getParam')->willReturnMap(
            [
                ['type', null, TemplateTypesInterface::TYPE_TEXT],
                ['text', null, 'Processed Template'],
                ['styles', null, '.class-name{color:red;}']
            ]
        );

        $this->template->expects($this->once())->method('setTemplateType')->with(TemplateTypesInterface::TYPE_TEXT)
            ->willReturnSelf();
        $this->template->expects($this->once())->method('setTemplateText')->with('Processed Template')
            ->willReturnSelf();
        $this->template->expects($this->once())->method('setTemplateStyles')->with('.class-name{color:red;}')
            ->willReturnSelf();
        $this->template->expects($this->atLeastOnce())->method('isPlain')->willReturn(true);
        $this->template->expects($this->atLeastOnce())->method('emulateDesign')->with(1);
        $this->template->expects($this->atLeastOnce())->method('revertDesign');

        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->atLeastOnce())->method('getId')->willReturn(1);

        $this->storeManager->expects($this->atLeastOnce())->method('getStores')->willReturn([$store]);


        $this->appState->expects($this->atLeastOnce())->method('emulateAreaCode')
            ->with(
                \Magento\Newsletter\Model\Template::DEFAULT_DESIGN_AREA,
                [
                    $this->template,
                    'getProcessedTemplate'
                ],
                [
                    [
                        'subscriber' => null
                    ]
                ]
            )
            ->willReturn('Processed Template');

        $this->assertEquals('<pre>Processed Template</pre>', $this->preview->toHtml());
    }

    public function testToHtmlWithSubscriber()
    {
        $this->request->expects($this->any())->method('getParam')->willReturnMap(
            [
                ['id', null, 2],
                ['store', null, 1],
                ['subscriber', null, 3]
            ]
        );
        $subscriber = $this->getMock('Magento\Newsletter\Model\Subscriber', [], [], '', false);
        $subscriber->expects($this->atLeastOnce())->method('load')->with(3)->willReturnSelf();
        $this->subscriberFactory->expects($this->atLeastOnce())->method('create')->willReturn($subscriber);

        $this->template->expects($this->atLeastOnce())->method('emulateDesign')->with(1);
        $this->template->expects($this->atLeastOnce())->method('revertDesign');

        $this->appState->expects($this->atLeastOnce())->method('emulateAreaCode')
            ->with(
                \Magento\Newsletter\Model\Template::DEFAULT_DESIGN_AREA,
                [
                    $this->template,
                    'getProcessedTemplate'
                ],
                [
                    [
                        'subscriber' => $subscriber
                    ]
                ]
            )
            ->willReturn('Processed Template');

        $this->assertEquals('Processed Template', $this->preview->toHtml());
    }
}
