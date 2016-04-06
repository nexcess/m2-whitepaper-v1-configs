<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Test\Unit\Component\Control;

use Magento\Ui\Component\Control\Action;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ActionTest
 */
class ActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Action
     */
    protected $action;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Set up
     */
    public function setUp()
    {
        $context = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\ContextInterface')
            ->getMockForAbstractClass();
        $processor = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->any())->method('getProcessor')->willReturn($processor);
        $this->objectManager = new ObjectManager($this);
        $this->action = $this->objectManager->getObject('Magento\Ui\Component\Control\Action', ['context' => $context]);
    }

    /**
     * Run test getComponentName method
     *
     * @return void
     */
    public function testGetComponentName()
    {
        $this->assertTrue($this->action->getComponentName() === Action::NAME);
    }
}
