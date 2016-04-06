<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Editor
 */
namespace Magento\Framework\Data\Test\Unit\Form\Element;

use Magento\Framework\Data\Form\Element\Editor;

class EditorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Editor
     */
    protected $model;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factoryMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    /**
     * @var \Magento\Framework\DataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formMock;

    /**
     * @var \Magento\Framework\DataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->factoryMock = $this->getMock('\Magento\Framework\Data\Form\Element\Factory', [], [], '', false);
        $this->collectionFactoryMock = $this->getMock(
            '\Magento\Framework\Data\Form\Element\CollectionFactory',
            [],
            [],
            '',
            false
        );
        $this->escaperMock = $this->getMock('\Magento\Framework\Escaper', [], [], '', false);
        $this->configMock = $this->getMock('\Magento\Framework\DataObject', ['getData'], [], '', false);

        $this->model = $this->objectManager->getObject(
            'Magento\Framework\Data\Form\Element\Editor',
            [
                'factoryElement' => $this->factoryMock,
                'factoryCollection' => $this->collectionFactoryMock,
                'escaper' => $this->escaperMock,
                'data' => ['config' => $this->configMock]
            ]
        );

        $this->formMock = $this->getMock(
            'Magento\Framework\Data\Form',
            ['getHtmlIdPrefix', 'getHtmlIdSuffix'],
            [],
            '',
            false,
            false
        );
        $this->model->setForm($this->formMock);
    }

    public function testConstruct()
    {
        $this->assertEquals('textarea', $this->model->getType());
        $this->assertEquals('textarea', $this->model->getExtType());
        $this->assertEquals(Editor::DEFAULT_ROWS, $this->model->getRows());
        $this->assertEquals(Editor::DEFAULT_COLS, $this->model->getCols());

        $this->configMock->expects($this->once())->method('getData')->with('enabled')->willReturn(true);

        $model = $this->objectManager->getObject(
            'Magento\Framework\Data\Form\Element\Editor',
            [
                'factoryElement' => $this->factoryMock,
                'factoryCollection' => $this->collectionFactoryMock,
                'escaper' => $this->escaperMock,
                'data' => ['config' => $this->configMock]
            ]
        );

        $this->assertEquals('wysiwyg', $model->getType());
        $this->assertEquals('wysiwyg', $model->getExtType());
    }

    public function testGetElementHtml()
    {
        $html = $this->model->getElementHtml();
        $this->assertContains('</textarea>', $html);
        $this->assertContains('rows="2"', $html);
        $this->assertContains('cols="15"', $html);
        $this->assertRegExp('/class=\".*textarea.*\"/i', $html);
        $this->assertNotRegExp('/.*mage\/adminhtml\/wysiwyg\/widget.*/i', $html);

        $this->configMock->expects($this->any())->method('getData')
            ->willReturnMap(
                [
                    ['enabled', null, true],
                    ['hidden', null, null]
                ]
            );
        $html = $this->model->getElementHtml();
        $this->assertRegExp('/.*mage\/adminhtml\/wysiwyg\/widget.*/i', $html);

        $this->configMock->expects($this->any())->method('getData')
            ->willReturnMap(
                [
                    ['enabled', null, null],
                    ['widget_window_url', null, 'localhost'],
                    ['add_widgets', null, true],
                    ['hidden', null, null]
                ]
            );
        $html = $this->model->getElementHtml();
        $this->assertRegExp('/.*mage\/adminhtml\/wysiwyg\/widget.*/i', $html);
    }

    public function testIsEnabled()
    {
        $this->assertEmpty($this->model->isEnabled());

        $this->model->setData('wysiwyg', true);
        $this->assertTrue($this->model->isEnabled());

        $this->model->unsetData('wysiwyg');
        $this->configMock->expects($this->once())->method('getData')->with('enabled')->willReturn(true);
        $this->assertTrue($this->model->isEnabled());
    }

    public function testIsHidden()
    {
        $this->assertEmpty($this->model->isHidden());

        $this->configMock->expects($this->once())->method('getData')->with('hidden')->willReturn(true);
        $this->assertTrue($this->model->isHidden());
    }

    public function testTranslate()
    {
        $this->assertEquals('Insert Image...', $this->model->translate('Insert Image...'));
    }

    public function testGetConfig()
    {
        $config = $this->getMock('\Magento\Framework\DataObject', ['getData'], [], '', false);
        $this->assertEquals($config, $this->model->getConfig());

        $this->configMock->expects($this->once())->method('getData')->with('test')->willReturn('test');
        $this->assertEquals('test', $this->model->getConfig('test'));
    }

    /**
     * Test protected `getTranslatedString` method via public `getElementHtml` method
     */
    public function testGetTranslatedString()
    {
        $this->configMock->expects($this->any())->method('getData')->withConsecutive(['enabled'])->willReturn(true);
        $html = $this->model->getElementHtml();
        $this->assertRegExp('/.*"Insert Image...":"Insert Image...".*/i', $html);
    }
}
