<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Framework\View\Test\Unit\Result;

/**
 * Class LayoutTest
 * @covers \Magento\Framework\View\Result\Layout
 */
class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\Layout
     */
    protected $layout;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Translate\InlineInterface
     */
    protected $translateInline;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\Result\Layout
     */
    protected $resultLayout;

    protected function setUp()
    {
        $this->layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $this->translateInline = $this->getMock('Magento\Framework\Translate\InlineInterface');

        $context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $context->expects($this->any())->method('getLayout')->will($this->returnValue($this->layout));
        $context->expects($this->any())->method('getRequest')->will($this->returnValue($this->request));
        $context->expects($this->any())->method('getEventManager')->will($this->returnValue($this->eventManager));

        $this->resultLayout = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject(
                'Magento\Framework\View\Result\Layout',
                ['context' => $context, 'translateInline' => $this->translateInline]
            );
    }

    /**
     * @covers \Magento\Framework\View\Result\Layout::getLayout()
     */
    public function testGetLayout()
    {
        $this->assertSame($this->layout, $this->resultLayout->getLayout());
    }

    public function testGetDefaultLayoutHandle()
    {
        $this->request->expects($this->once())
            ->method('getFullActionName')
            ->willReturn('Module_Controller_Action');

        $this->assertEquals('module_controller_action', $this->resultLayout->getDefaultLayoutHandle());
    }

    public function testAddHandle()
    {
        $processor = $this->getMock('Magento\Framework\View\Layout\ProcessorInterface', [], [], '', false);
        $processor->expects($this->once())->method('addHandle')->with('module_controller_action');

        $this->layout->expects($this->once())->method('getUpdate')->will($this->returnValue($processor));

        $this->assertSame($this->resultLayout, $this->resultLayout->addHandle('module_controller_action'));
    }

    public function testAddUpdate()
    {
        $processor = $this->getMock('Magento\Framework\View\Layout\ProcessorInterface', [], [], '', false);
        $processor->expects($this->once())->method('addUpdate')->with('handle_name');

        $this->layout->expects($this->once())->method('getUpdate')->will($this->returnValue($processor));

        $this->resultLayout->addUpdate('handle_name');
    }

    /**
     * @param int|string $httpCode
     * @param string $headerName
     * @param string $headerValue
     * @param bool $replaceHeader
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $setHttpResponseCodeCount
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $setHeaderCount
     * @dataProvider renderResultDataProvider
     */
    public function testRenderResult(
        $httpCode, $headerName, $headerValue, $replaceHeader, $setHttpResponseCodeCount, $setHeaderCount
    ) {
        $layoutOutput = 'output';

        $this->layout->expects($this->once())->method('getOutput')->will($this->returnValue($layoutOutput));

        $this->request->expects($this->once())->method('getFullActionName')
            ->will($this->returnValue('Module_Controller_Action'));

        $this->eventManager->expects($this->exactly(2))->method('dispatch')->withConsecutive(
            ['layout_render_before'],
            ['layout_render_before_Module_Controller_Action']
        );

        $this->translateInline->expects($this->once())
            ->method('processResponseBody')
            ->with($layoutOutput)
            ->willReturnSelf();

        /** @var \Magento\Framework\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject $response */
        $response = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);
        $response->expects($setHttpResponseCodeCount)->method('setHttpResponseCode')->with($httpCode);
        $response->expects($setHeaderCount)->method('setHeader')->with($headerName, $headerValue, $replaceHeader);
        $response->expects($this->once())->method('appendBody')->with($layoutOutput);

        $this->resultLayout->setHttpResponseCode($httpCode);

        if ($headerName && $headerValue) {
            $this->resultLayout->setHeader($headerName, $headerValue, $replaceHeader);
        }

        $this->resultLayout->renderResult($response);
    }

    /**
     * @return array
     */
    public function renderResultDataProvider()
    {
        return [
            [200, 'content-type', 'text/html', true, $this->once(), $this->once()],
            [0, '', '', false, $this->never(), $this->never()]
        ];
    }

    public function testAddDefaultHandle()
    {
        $processor = $this->getMock('Magento\Framework\View\Layout\ProcessorInterface', [], [], '', false);
        $processor->expects($this->once())->method('addHandle')->with('module_controller_action');

        $this->layout->expects($this->once())->method('getUpdate')->will($this->returnValue($processor));

        $this->request->expects($this->once())->method('getFullActionName')
            ->will($this->returnValue('Module_Controller_Action'));

        $this->assertSame($this->resultLayout, $this->resultLayout->addDefaultHandle());
    }
}
