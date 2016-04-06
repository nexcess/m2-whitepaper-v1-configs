<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Test\Unit\Model\Design\Backend;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Theme\Model\Design\Backend\Exceptions;
use Magento\Framework\App\Area;

class ExceptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Model\Design\Backend\Exceptions
     */
    protected $model;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $designMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMockBuilder('Magento\Framework\Model\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->designMock = $this->getMockBuilder('Magento\Framework\View\DesignInterface')->getMock();
        $this->contextMock->expects($this->once())
            ->method('getEventDispatcher')
            ->willReturn($this->getMockBuilder('Magento\Framework\Event\ManagerInterface')->getMock());

        $this->model = (new ObjectManager($this))->getObject(
            'Magento\Theme\Model\Design\Backend\Exceptions',
            [
                'context' => $this->contextMock,
                'design' => $this->designMock,
            ]
        );
    }

    /**
     * @test
     * @return void
     * @covers \Magento\Theme\Model\Design\Backend\Exceptions::beforeSave
     * @covers \Magento\Theme\Model\Design\Backend\Exceptions::_composeRegexp
     * @covers \Magento\Theme\Model\Design\Backend\Exceptions::_isRegexp
     * @covers \Magento\Theme\Model\Design\Backend\Exceptions::__construct
     */
    public function testBeforeSave()
    {
        $value = ['__empty' => '', 'test' => ['search' => '1qwe', 'value' => '#val#', 'regexp' => '[a-zA-Z0-9]*']];
        $this->designMock->expects($this->once())
            ->method('setDesignTheme')
            ->with('#val#', Area::AREA_FRONTEND);
        $this->model->setValue($value);
        $this->model->beforeSave();
    }
}
