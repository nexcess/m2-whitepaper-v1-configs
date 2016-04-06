<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Framework\View\Test\Unit\File\Collector\Decorator;

class ModuleOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\File\Collector\Decorator\ModuleOutput
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileSource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_moduleManager;

    protected function setUp()
    {
        $this->_fileSource = $this->getMockForAbstractClass('Magento\Framework\View\File\CollectorInterface');
        $this->_moduleManager = $this->getMock('Magento\Framework\Module\Manager', [], [], '', false);
        $this->_moduleManager
            ->expects($this->any())
            ->method('isOutputEnabled')
            ->will($this->returnValueMap([
                ['Module_OutputEnabled', true],
                ['Module_OutputDisabled', false],
            ]));
        $this->_model = new \Magento\Framework\View\File\Collector\Decorator\ModuleOutput(
            $this->_fileSource, $this->_moduleManager
        );
    }

    public function testGetFiles()
    {
        $theme = $this->getMockForAbstractClass('Magento\Framework\View\Design\ThemeInterface');
        $fileOne = new \Magento\Framework\View\File('1.xml', 'Module_OutputEnabled');
        $fileTwo = new \Magento\Framework\View\File('2.xml', 'Module_OutputDisabled');
        $fileThree = new \Magento\Framework\View\File('3.xml', 'Module_OutputEnabled', $theme);
        $this->_fileSource
            ->expects($this->once())
            ->method('getFiles')
            ->with($theme, '*.xml')
            ->will($this->returnValue([$fileOne, $fileTwo, $fileThree]));
        $this->assertSame([$fileOne, $fileThree], $this->_model->getFiles($theme, '*.xml'));
    }
}
