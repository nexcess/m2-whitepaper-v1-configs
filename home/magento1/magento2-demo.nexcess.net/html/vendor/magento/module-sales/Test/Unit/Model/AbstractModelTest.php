<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Test\Unit\Model;

/**
 * Class AbstractModelTest
 */
class AbstractModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $model;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Sales\Model\Order');
    }

    public function testGetEventPrefix()
    {
        $this->assertEquals('sales_order', $this->model->getEventPrefix());
    }

    public function testGetEventObject()
    {
        $this->assertEquals('order', $this->model->getEventObject());
    }
}
