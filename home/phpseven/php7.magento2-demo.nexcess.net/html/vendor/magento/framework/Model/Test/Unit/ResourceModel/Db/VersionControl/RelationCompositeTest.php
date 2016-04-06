<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\Test\Unit\ResourceModel\Db\VersionControl;

/**
 * Class RelationCompositeTest
 */
class RelationCompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite
     */
    protected $entityRelationComposite;

    /**
     * @var \Magento\Framework\Model\AbstractModel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modelMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface
     */
    protected $relationProcessorMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    public function setUp()
    {
        $this->modelMock = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getEventPrefix'
                ]
            )
            ->getMockForAbstractClass();
        $this->relationProcessorMock = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->eventManagerMock = $this->getMockBuilder('Magento\Framework\Event\ManagerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->relationProcessorMock = $this->getMockBuilder(
            'Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface'
        )->disableOriginalConstructor()->getMockForAbstractClass();

        $this->entityRelationComposite = new \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite(
            $this->eventManagerMock,
            [
                'default' => $this->relationProcessorMock
            ]
        );
    }

    public function testProcessRelations()
    {
        $this->relationProcessorMock->expects($this->once())
            ->method('processRelation')
            ->with($this->modelMock);
        $this->modelMock->expects($this->once())
            ->method('getEventPrefix')
            ->willReturn('custom_event_prefix');
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with(
                'custom_event_prefix_process_relation',
                [
                    'object' => $this->modelMock
                ]
            );
        $this->entityRelationComposite->processRelations($this->modelMock);
    }
}
