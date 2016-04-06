<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Test\Unit\Model\Product\Attribute;

use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Group
     */
    private $model;

    public function testHasSystemAttributes()
    {
        $this->model->setId(1);
        $this->assertTrue($this->model->hasSystemAttributes());
    }

    protected function setUp()
    {
        $helper = new ObjectManager($this);
        $this->model = $helper->getObject(
            '\Magento\Catalog\Model\Product\Attribute\Group',
            [
                'attributeCollectionFactory' => $this->getMockedCollectionFactory()
            ]
        );
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private function getMockedCollectionFactory()
    {
        $mockedCollection = $this->getMockedCollection();

        $mockBuilder = $this->getMockBuilder(
            '\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory'
        );
        $mock = $mockBuilder->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($mockedCollection));

        return $mock;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    private function getMockedCollection()
    {
        $mockBuilder = $this->getMockBuilder('\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection');
        $mock = $mockBuilder->disableOriginalConstructor()
            ->getMock();

        $item = new DataObject();
        $item->setIsUserDefine(false);

        $mock->expects($this->any())
            ->method('setAttributeGroupFilter')
            ->will($this->returnValue($mock));
        $mock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$item])));

        return $mock;
    }
}
