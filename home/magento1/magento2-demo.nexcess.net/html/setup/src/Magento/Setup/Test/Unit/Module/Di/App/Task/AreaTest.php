<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Test\Unit\Module\Di\App\Task;

use Magento\Framework\App;
use Magento\Setup\Module\Di\App\Task\Operation\Area;
use Magento\Setup\Module\Di\Compiler\Config;

class AreaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var App\AreaList | \PHPUnit_Framework_MockObject_MockObject
     */
    private $areaListMock;

    /**
     * @var \Magento\Setup\Module\Di\Code\Reader\Decorator\Area | \PHPUnit_Framework_MockObject_MockObject
     */
    private $areaInstancesNamesList;

    /**
     * @var Config\Reader | \PHPUnit_Framework_MockObject_MockObject
     */
    private $configReaderMock;

    /**
     * @var Config\WriterInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $configWriterMock;

    /**
     * @var \Magento\Setup\Module\Di\Compiler\Config\ModificationChain | \PHPUnit_Framework_MockObject_MockObject
     */
    private $configChain;

    protected function setUp()
    {
        $this->areaListMock = $this->getMockBuilder('Magento\Framework\App\AreaList')
            ->disableOriginalConstructor()
            ->getMock();
        $this->areaInstancesNamesList = $this->getMockBuilder('\Magento\Setup\Module\Di\Code\Reader\Decorator\Area')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configReaderMock = $this->getMockBuilder('Magento\Setup\Module\Di\Compiler\Config\Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configWriterMock = $this->getMockBuilder('Magento\Setup\Module\Di\Compiler\Config\WriterInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configChain = $this->getMockBuilder('Magento\Setup\Module\Di\Compiler\Config\ModificationChain')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testDoOperationEmptyPath()
    {
        $areaOperation = new Area(
            $this->areaListMock,
            $this->areaInstancesNamesList,
            $this->configReaderMock,
            $this->configWriterMock,
            $this->configChain
        );

        $this->assertNull($areaOperation->doOperation());
    }

    public function testDoOperationGlobalArea()
    {
        $path = 'path/to/codebase/';
        $arguments = ['class' => []];
        $generatedConfig = [
            'arguments' => $arguments,
            'preferences' => [],
            'instanceTypes' => []
        ];

        $areaOperation = new Area(
            $this->areaListMock,
            $this->areaInstancesNamesList,
            $this->configReaderMock,
            $this->configWriterMock,
            $this->configChain,
            [$path]
        );

        $this->areaListMock->expects($this->once())
            ->method('getCodes')
            ->willReturn([]);
        $this->areaInstancesNamesList->expects($this->once())
            ->method('getList')
            ->with($path)
            ->willReturn($arguments);
        $this->configReaderMock->expects($this->once())
            ->method('generateCachePerScope')
            ->with(
                $this->isInstanceOf('Magento\Setup\Module\Di\Definition\Collection'),
                App\Area::AREA_GLOBAL
            )
            ->willReturn($generatedConfig);
        $this->configChain->expects($this->once())
            ->method('modify')
            ->with($generatedConfig)
            ->willReturn($generatedConfig);

        $this->configWriterMock->expects($this->once())
            ->method('write')
            ->with(
                App\Area::AREA_GLOBAL,
                $generatedConfig
            );

        $areaOperation->doOperation();
    }
}
