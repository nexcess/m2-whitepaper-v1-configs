<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Search\Test\Unit\Adapter\Mysql\Aggregation;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderContainer;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\EntityMetadata|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityMetadata;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    /**
     * @var \Magento\Framework\Search\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Magento\Framework\Search\Request\BucketInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bucket;

    /**
     * @var DataProviderContainer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProviderContainer;

    /**
     * @var DataProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProvider;

    /**
     * @var Builder\Container|\PHPUnit_Framework_MockObject_MockObject
     */
    private $aggregationContainer;

    /**
     * @var Builder\BucketInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bucketBuilder;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder
     */
    private $builder;

    /**
     * SetUP method
     */
    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->entityMetadata = $this->getMockBuilder('Magento\Framework\Search\EntityMetadata')
            ->setMethods(['getEntityId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Magento\Framework\Search\RequestInterface')
            ->setMethods(['getAggregation'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->bucket = $this->getMockBuilder('Magento\Framework\Search\Request\BucketInterface')
            ->setMethods(['getName'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();

        $this->bucketBuilder = $this->getMockBuilder(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\BucketInterface'
        )
            ->setMethods(['build'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->aggregationContainer = $this->getMockBuilder(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Container'
        )
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->aggregationContainer->expects($this->any())->method('get')->willReturn($this->bucketBuilder);

        $this->connectionMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->setMethods(['fetchAssoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->dataProvider = $this->getMockBuilder(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface'
        )
            ->setMethods(['getDataSet'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->dataProviderContainer = $this->getMockBuilder(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderContainer'
        )
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataProviderContainer->expects($this->any())->method('get')->willReturn($this->dataProvider);

        $this->resource = $this->getMockBuilder('Magento\Framework\App\ResourceConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource->expects($this->any())->method('getConnection')->willReturn($this->connectionMock);

        $this->builder = $helper->getObject(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder',
            [
                'entityMetadata' => $this->entityMetadata,
                'dataProviderContainer' => $this->dataProviderContainer,
                'resource' => $this->resource,
                'aggregationContainer' => $this->aggregationContainer
            ]
        );
    }

    /**
     * Test for method "build"
     */
    public function testBuild()
    {
        $fetchResult = ['name' => ['some', 'result']];

        /** @var \Magento\Framework\DB\Ddl\Table|\PHPUnit_Framework_MockObject_MockObject $table */
        $table = $this->getMockBuilder('Magento\Framework\DB\Ddl\Table')
            ->disableOriginalConstructor()
            ->getMock();

        $this->bucket->expects($this->once())->method('getName')->willReturn('name');
        $this->request->expects($this->once())->method('getAggregation')->willReturn([$this->bucket]);
        $this->request->expects($this->once())->method('getDimensions')->willReturn([]);
        $this->bucketBuilder->expects($this->once())->method('build')->willReturn($fetchResult['name']);

        $result = $this->builder->build($this->request, $table);

        $this->assertEquals($result, $fetchResult);
    }
}
