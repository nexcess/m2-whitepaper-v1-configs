<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Indexer\Test\Unit\Model;

use Magento\Framework\Indexer\StateInterface;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Indexer\Model\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Framework\Indexer\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerFactoryMock;

    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexersFactoryMock;

    /**
     * @var \Magento\Framework\Mview\ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewProcessorMock;

    protected function setUp()
    {
        $this->configMock = $this->getMockForAbstractClass(
            'Magento\Framework\Indexer\ConfigInterface',
            [],
            '',
            false,
            false,
            true,
            ['getIndexers']
        );
        $this->indexerFactoryMock = $this->getMock(
            'Magento\Indexer\Model\IndexerFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->indexersFactoryMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->viewProcessorMock = $this->getMockForAbstractClass(
            'Magento\Framework\Mview\ProcessorInterface',
            [],
            '',
            false
        );
        $this->model = new \Magento\Indexer\Model\Processor(
            $this->configMock,
            $this->indexerFactoryMock,
            $this->indexersFactoryMock,
            $this->viewProcessorMock
        );
    }

    public function testReindexAllInvalid()
    {
        $indexers = ['indexer1' => [], 'indexer2' => []];

        $this->configMock->expects($this->once())->method('getIndexers')->will($this->returnValue($indexers));

        $state1Mock = $this->getMock(
            'Magento\Indexer\Model\Indexer\State',
            ['getStatus', '__wakeup'],
            [],
            '',
            false
        );
        $state1Mock->expects(
            $this->once()
        )->method(
            'getStatus'
        )->will(
            $this->returnValue(StateInterface::STATUS_INVALID)
        );
        $indexer1Mock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            ['load', 'getState', 'reindexAll'],
            [],
            '',
            false
        );
        $indexer1Mock->expects($this->once())->method('getState')->will($this->returnValue($state1Mock));
        $indexer1Mock->expects($this->once())->method('reindexAll');

        $state2Mock = $this->getMock(
            'Magento\Indexer\Model\Indexer\State',
            ['getStatus', '__wakeup'],
            [],
            '',
            false
        );
        $state2Mock->expects(
            $this->once()
        )->method(
            'getStatus'
        )->will(
            $this->returnValue(StateInterface::STATUS_VALID)
        );
        $indexer2Mock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            ['load', 'getState', 'reindexAll'],
            [],
            '',
            false
        );
        $indexer2Mock->expects($this->never())->method('reindexAll');
        $indexer2Mock->expects($this->once())->method('getState')->will($this->returnValue($state2Mock));

        $this->indexerFactoryMock->expects($this->at(0))->method('create')->will($this->returnValue($indexer1Mock));
        $this->indexerFactoryMock->expects($this->at(1))->method('create')->will($this->returnValue($indexer2Mock));

        $this->model->reindexAllInvalid();
    }

    public function testReindexAll()
    {
        $indexerMock = $this->getMock('Magento\Indexer\Model\Indexer', [], [], '', false);
        $indexerMock->expects($this->exactly(2))->method('reindexAll');
        $indexers = [$indexerMock, $indexerMock];

        $indexersMock = $this->getMock('Magento\Indexer\Model\Indexer\Collection', [], [], '', false);
        $this->indexersFactoryMock->expects($this->once())->method('create')->will($this->returnValue($indexersMock));
        $indexersMock->expects($this->once())->method('getItems')->will($this->returnValue($indexers));

        $this->model->reindexAll();
    }

    public function testUpdateMview()
    {
        $this->viewProcessorMock->expects($this->once())->method('update')->with('indexer')->willReturnSelf();
        $this->model->updateMview();
    }

    public function testClearChangelog()
    {
        $this->viewProcessorMock->expects($this->once())->method('clearChangelog')->with('indexer')->willReturnSelf();
        $this->model->clearChangelog();
    }
}
