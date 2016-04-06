<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Test\Unit\Model\ResourceModel\Category\Collection;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection\Factory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->_model = new \Magento\Catalog\Model\ResourceModel\Category\Collection\Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $objectOne = $this->getMock('Magento\Catalog\Model\ResourceModel\Category\Collection', [], [], '', false);
        $objectTwo = $this->getMock('Magento\Catalog\Model\ResourceModel\Category\Collection', [], [], '', false);
        $this->_objectManager->expects(
            $this->exactly(2)
        )->method(
            'create'
        )->with(
            'Magento\Catalog\Model\ResourceModel\Category\Collection',
            []
        )->will(
            $this->onConsecutiveCalls($objectOne, $objectTwo)
        );
        $this->assertSame($objectOne, $this->_model->create());
        $this->assertSame($objectTwo, $this->_model->create());
    }
}
