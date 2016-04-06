<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Test\Unit\View\Asset\MaterializationStrategy;

use \Magento\Framework\App\View\Asset\MaterializationStrategy\Factory;

use Magento\Framework\ObjectManagerInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->setMethods([])
            ->getMock();
    }

    public function testCreateEmptyStrategies()
    {
        $asset = $this->getAsset();
        $copyStrategy = $this->getMockBuilder('Magento\Framework\App\View\Asset\MaterializationStrategy\Copy')
            ->setMethods([])
            ->getMock();
        $copyStrategy->expects($this->once())
            ->method('isSupported')
            ->with($asset)
            ->willReturn(true);

        $this->objectManager->expects($this->once())
            ->method('get')
            ->with(Factory::DEFAULT_STRATEGY)
            ->willReturn($copyStrategy);

        $factory = new Factory($this->objectManager, []);
        $this->assertSame($copyStrategy, $factory->create($asset));
    }

    public function testCreateSupported()
    {
        $asset = $this->getAsset();
        $copyStrategy = $this->getMockBuilder('Magento\Framework\App\View\Asset\MaterializationStrategy\Copy')
            ->setMethods([])
            ->getMock();
        $copyStrategy->expects($this->once())
            ->method('isSupported')
            ->with($asset)
            ->willReturn(false);

        $supportedStrategy = $this->getMockBuilder(
            'Magento\Framework\App\View\Asset\MaterializationStrategy\StrategyInterface'
        )
            ->setMethods([])
            ->getMock();
        $supportedStrategy->expects($this->once())
            ->method('isSupported')
            ->with($asset)
            ->willReturn(true);

        $factory = new Factory($this->objectManager, [$copyStrategy, $supportedStrategy]);
        $this->assertSame($supportedStrategy, $factory->create($asset));
    }

    public function testCreateException()
    {
        $asset = $this->getAsset();
        $copyStrategy = $this->getMockBuilder('Magento\Framework\App\View\Asset\MaterializationStrategy\Copy')
            ->setMethods([])
            ->getMock();
        $copyStrategy->expects($this->once())
            ->method('isSupported')
            ->with($asset)
            ->willReturn(false);

        $this->objectManager->expects($this->once())
            ->method('get')
            ->with(Factory::DEFAULT_STRATEGY)
            ->willReturn($copyStrategy);

        $factory = new Factory($this->objectManager, []);

        $this->setExpectedException('LogicException', 'No materialization strategy is supported');
        $factory->create($asset);
    }

    /**
     * @return \Magento\Framework\View\Asset\LocalInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAsset()
    {
        return $this->getMockBuilder('Magento\Framework\View\Asset\LocalInterface')
            ->setMethods([])
            ->getMock();
    }
}
