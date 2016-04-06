<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Test\Unit\Model\ResourceModel\Report\Collection;

class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested collection
     *
     * @var \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection
     */
    protected $collection;

    protected function setUp()
    {
        $entityFactory = $this->getMock('\Magento\Framework\Data\Collection\EntityFactory', [], [], '', false);
        $logger = $this->getMock('\Psr\Log\LoggerInterface', [], [], '', false);
        $fetchStrategy = $this->getMock('\Magento\Framework\Data\Collection\Db\FetchStrategy\Query', [], [], '', false);
        $eventManager = $this->getMock('\Magento\Framework\Event\Manager', [], [], '', false);
        $connection = $this->getMock('\Magento\Framework\DB\Adapter\Pdo\Mysql', [], [], '', false);

        $resource = $this->getMockBuilder('\Magento\Framework\Model\ResourceModel\Db\AbstractDb')
            ->disableOriginalConstructor()
            ->setMethods(['getConnection'])
            ->getMockForAbstractClass();
        $resource->method('getConnection')->willReturn($connection);

        $this->collection = new \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    public function testIsSubtotalsGetDefault()
    {
        $this->assertFalse($this->collection->isSubTotals());
    }

    public function testSetIsSubtotals()
    {
        $this->collection->setIsSubTotals(true);
        $this->assertTrue($this->collection->isSubTotals());

        $this->collection->setIsSubTotals(false);
        $this->assertFalse($this->collection->isSubTotals());
    }
}
