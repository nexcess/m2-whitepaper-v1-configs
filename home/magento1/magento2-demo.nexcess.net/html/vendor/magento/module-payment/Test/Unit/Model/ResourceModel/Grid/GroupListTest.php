<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Test\Unit\Model\ResourceModel\Grid;

class GroupListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\ResourceModel\Grid\GroupsList
     */
    protected $groupArrayModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    protected function setUp()
    {
        $this->helperMock = $this->getMock('Magento\Payment\Helper\Data', [], [], '', false);
        $this->groupArrayModel = new \Magento\Payment\Model\ResourceModel\Grid\GroupList($this->helperMock);
    }

    public function testToOptionArray()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('getPaymentMethodList')
            ->with(true, true, true)
            ->will($this->returnValue(['group data']));
        $this->assertEquals(['group data'], $this->groupArrayModel->toOptionArray());
    }
}
