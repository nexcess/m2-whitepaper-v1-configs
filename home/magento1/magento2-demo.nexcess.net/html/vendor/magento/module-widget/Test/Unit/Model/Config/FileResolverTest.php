<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Test\Unit\Model\Config;

use Magento\Framework\Component\ComponentRegistrar;
use \Magento\Widget\Model\Config\FileResolver;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileResolver
     */
    private $object;

    /**
     * @var \Magento\Framework\Module\Dir\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleReader;

    /**
     * @var \Magento\Framework\Config\FileIteratorFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var \Magento\Framework\Component\DirSearch|\PHPUnit_Framework_MockObject_MockObject
     */
    private $componentDirSearch;

    protected function setUp()
    {
        $this->moduleReader = $this->getMock('Magento\Framework\Module\Dir\Reader', [], [], '', false);
        $this->factory = $this->getMock('Magento\Framework\Config\FileIteratorFactory', [], [], '', false);
        $this->componentDirSearch = $this->getMock('\Magento\Framework\Component\DirSearch', [], [], '', false);
        $this->object = new FileResolver($this->moduleReader, $this->factory, $this->componentDirSearch);
    }

    public function testGetGlobal()
    {
        $expected = new \StdClass();
        $this->moduleReader
            ->expects($this->once())
            ->method('getConfigurationFiles')
            ->with('file')
            ->willReturn($expected);
        $this->assertSame($expected, $this->object->get('file', 'global'));
    }

    public function testGetDesign()
    {
        $expected = new \StdClass();
        $this->componentDirSearch->expects($this->once())
            ->method('collectFiles')
            ->with(ComponentRegistrar::THEME, 'etc/file')
            ->will($this->returnValue(['test']));
        $this->factory->expects($this->once())->method('create')->with(['test'])->willReturn($expected);
        $this->assertSame($expected, $this->object->get('file', 'design'));
    }

    public function testGetDefault()
    {
        $expected = new \StdClass();
        $this->factory->expects($this->once())->method('create')->with([])->willReturn($expected);
        $this->assertSame($expected, $this->object->get('file', 'unknown'));
    }
}
