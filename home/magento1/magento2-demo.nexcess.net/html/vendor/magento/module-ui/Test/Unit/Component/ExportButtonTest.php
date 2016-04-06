<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Test\Unit\Component;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ExportButtonTest
 */
class ExportButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Ui\Component\ExportButton
     */
    protected $model;

    protected function setUp()
    {
        $context = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\ContextInterface')
            ->getMockForAbstractClass();
        $processor = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->any())->method('getProcessor')->willReturn($processor);
        $this->objectManager = new ObjectManager($this);

        $this->urlBuilderMock = $this->getMockBuilder('Magento\Framework\UrlInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $this->objectManager->getObject(
            'Magento\Ui\Component\ExportButton',
            [
                'urlBuilder' => $this->urlBuilderMock,
                'context' => $context,
            ]
        );
    }

    public function testGetComponentName()
    {
        $this->assertEquals(\Magento\Ui\Component\ExportButton::NAME, $this->model->getComponentName());
    }

    public function testPrepare()
    {
        $option = ['label' => 'test label', 'value' => 'test value', 'url' => 'test_url'];
        $data = ['config' => ['options' => [$option]]];
        $this->model->setData($data);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('test_url')
            ->willReturnArgument(0);
        $this->assertNull($this->model->prepare());
    }
}
