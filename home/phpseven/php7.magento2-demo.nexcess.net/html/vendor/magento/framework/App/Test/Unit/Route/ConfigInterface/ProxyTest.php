<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Test\Unit\Route\ConfigInterface;

class ProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Route\ConfigInterface\Proxy
     */
    protected $_proxy;

    /**
     * @var \Magento\Framework\App\Route\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    public function setUp()
    {
        $this->_object = $this->getMock(
            '\Magento\Framework\App\Route\ConfigInterface',
            ['getRouteFrontName', 'getRouteByFrontName', 'getModulesByFrontName'],
            [],
            '',
            false
        );

        $objectManager = $this->getMock('\Magento\Framework\ObjectManager\ObjectManager', ['get'], [], '', false);
        $objectManager->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\App\Route\ConfigInterface')
            ->will($this->returnValue($this->_object));

        $this->_proxy = new \Magento\Framework\App\Route\ConfigInterface\Proxy(
            $objectManager,
            'Magento\Framework\App\Route\ConfigInterface'
        );
    }

    public function testGetRouteFrontName()
    {
        $routeId = 1;
        $scope = null;
        $this->_object->expects($this->once())->method('getRouteFrontName')->with($routeId, $scope);
        $this->_proxy->getRouteFrontName($routeId, $scope);
    }

    public function testGetRouteByFrontName()
    {
        $frontName = 'route';
        $scope = null;
        $this->_object->expects($this->once())->method('getRouteByFrontName')->with($frontName, $scope);
        $this->_proxy->getRouteByFrontName($frontName, $scope);
    }

    public function testGetModulesByFrontName()
    {
        $frontName = 'route';
        $scope = null;
        $this->_object->expects($this->once())->method('getModulesByFrontName')->with($frontName, $scope);
        $this->_proxy->getModulesByFrontName($frontName, $scope);
    }
}
