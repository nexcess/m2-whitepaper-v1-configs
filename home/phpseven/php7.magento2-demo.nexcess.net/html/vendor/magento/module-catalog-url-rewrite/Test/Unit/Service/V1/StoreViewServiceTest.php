<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogUrlRewrite\Test\Unit\Service\V1;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class StoreViewServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Service\V1\StoreViewService */
    protected $storeViewService;

    /** @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $config;

    /** @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject */
    protected $resource;

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $connection;

    /** @var  \Magento\Framework\Db\Select|\PHPUnit_Framework_MockObject_MockObject */
    protected $select;

    protected function setUp()
    {
        $this->config = $this->getMock('Magento\Eav\Model\Config', [], [], '', false);
        $this->select = $this->getMock('Magento\Framework\Db\Select', [], [], '', false);
        $this->connection = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $this->resource = $this->getMock('Magento\Framework\App\ResourceConnection', [], [], '', false);
        $this->resource->expects($this->any())->method('getConnection')->will($this->returnValue($this->connection));

        $this->storeViewService = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Service\V1\StoreViewService',
            [
                'eavConfig' => $this->config,
                'resource' => $this->resource,
            ]
        );
    }

    /**
     * @return array
     */
    public function isRootCategoryForStoreDataProvider()
    {
        return [
            [1, 1, 1, true],
            [1, 2, 1, false],
            [2, 0, 1, false],
        ];
    }

    /**
     * @return array
     */
    public function overriddenUrlKeyForStoreDataProvider()
    {
        return [
            [1, [1, 2], true],
            [1, [2, 3], false],
            [1, [], false],
        ];
    }

    /**
     * @dataProvider overriddenUrlKeyForStoreDataProvider
     * @param int $storeId
     * @param array $fetchedStoreIds
     * @param bool $result
     */
    public function testDoesEntityHaveOverriddenUrlKeyForStore($storeId, $fetchedStoreIds, $result)
    {
        $entityType = 'entity_type';
        $productId = 'product_id';
        $attribute = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'getBackendTable', 'getId'])
            ->getMockForAbstractClass();
        $this->config->expects($this->once())->method('getAttribute')->with($entityType, 'url_key')
            ->will($this->returnValue($attribute));
        $attribute->expects($this->once())->method('getBackendTable')->will($this->returnValue('backend_table'));
        $attribute->expects($this->once())->method('getId')->will($this->returnValue('attribute-id'));
        $this->select->expects($this->once())->method('from')->with('backend_table', 'store_id')
            ->will($this->returnSelf());
        $this->select->expects($this->any())->method('where')->will($this->returnSelf());
        $this->connection->expects($this->once())->method('select')->will($this->returnValue($this->select));
        $this->connection->expects($this->once())->method('fetchCol')->will($this->returnValue($fetchedStoreIds));

        $this->assertEquals(
            $result,
            $this->storeViewService->doesEntityHaveOverriddenUrlKeyForStore($storeId, $productId, $entityType)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot retrieve attribute for entity type "invalid_type"
     */
    public function testInvalidAttributeRetrieve()
    {
        $invalidEntityType = 'invalid_type';
        $this->config->expects($this->once())->method('getAttribute')->will($this->returnValue(false));

        $this->storeViewService->doesEntityHaveOverriddenUrlKeyForStore(1, 1, $invalidEntityType);
    }
}
