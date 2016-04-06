<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Filesystem\Test\Unit\File;

use \Magento\Framework\Filesystem\File\WriteFactory;

/**
 * Class WriteFactoryTest
 */
class WriteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $driverPool = $this->getMock('Magento\Framework\Filesystem\DriverPool', ['getDriver']);
        $driverPool->expects($this->never())->method('getDriver');
        $factory = new WriteFactory($driverPool);
        $driver = $this->getMockForAbstractClass('Magento\Framework\Filesystem\DriverInterface');
        $driver->expects($this->any())->method('isExists')->willReturn(true);
        $result = $factory->create('path', $driver);
        $this->assertInstanceOf('Magento\Framework\Filesystem\File\Write', $result);
    }

    public function testCreateWithDriverCode()
    {
        $driverPool = $this->getMock('Magento\Framework\Filesystem\DriverPool', ['getDriver']);
        $driverMock = $this->getMockForAbstractClass('Magento\Framework\Filesystem\DriverInterface');
        $driverMock->expects($this->any())->method('isExists')->willReturn(true);
        $driverPool->expects($this->once())->method('getDriver')->willReturn($driverMock);
        $factory = new WriteFactory($driverPool);
        $result = $factory->create('path', 'driverCode');
        $this->assertInstanceOf('Magento\Framework\Filesystem\File\Write', $result);
    }

    public function testCreateWithMode()
    {
        $driverPool = $this->getMock('Magento\Framework\Filesystem\DriverPool', ['getDriver']);
        $driverPool->expects($this->never())->method('getDriver');
        $driver = $this->getMockForAbstractClass('Magento\Framework\Filesystem\DriverInterface');
        $driver->expects($this->any())->method('isExists')->willReturn(true);
        $factory = new WriteFactory($driverPool);
        $result = $factory->create('path', $driver, 'a+');
        $this->assertInstanceOf('Magento\Framework\Filesystem\File\Write', $result);
    }
}
