<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Indexer\Test\Unit;

class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Indexer\ActionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->model = new \Magento\Framework\Indexer\ActionFactory($this->objectManagerMock);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage NotAction doesn't implement \Magento\Framework\Indexer\ActionInterface
     */
    public function testGetWithException()
    {
        $notActionInterfaceMock = $this->getMock('NotAction', [], [], '', false);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('NotAction', [])
            ->willReturn($notActionInterfaceMock);
        $this->model->create('NotAction');
    }

    public function testCreate()
    {
        $actionInterfaceMock = $this->getMockForAbstractClass(
            'Magento\Framework\Indexer\ActionInterface',
            [],
            '',
            false
        );
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Framework\Indexer\ActionInterface', [])
            ->willReturn($actionInterfaceMock);
        $this->model->create('Magento\Framework\Indexer\ActionInterface');
        $this->assertInstanceOf('Magento\Framework\Indexer\ActionInterface', $actionInterfaceMock);
    }
}
