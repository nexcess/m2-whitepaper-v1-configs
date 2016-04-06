<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Model\Config\Source\Address;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Config\Source\Address\Type
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new \Magento\Customer\Model\Config\Source\Address\Type();
    }

    public function testToOptionArray()
    {
        $expected = ['billing' => 'Billing Address','shipping' => 'Shipping Address'];
        $this->assertEquals($expected, $this->model->toOptionArray());
    }
}
