<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Test\Unit\Model\Theme;

use Magento\Framework\App\Area;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Theme\Model\Theme\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Data
     */
    protected $model;

    protected function setUp()
    {
        $this->model = (new ObjectManager($this))->getObject('Magento\Theme\Model\Theme\Data');
    }

    /**
     * @test
     * @return void
     */
    public function testGetArea()
    {
        $area = Area::AREA_FRONTEND;
        $this->model->setArea($area);
        $this->assertEquals($area, $this->model->getArea());
    }
}
