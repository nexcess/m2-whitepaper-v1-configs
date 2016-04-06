<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Event\Magento.
 */
namespace Magento\Test\Event;

class MagentoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Event\Magento
     */
    protected $_object;

    /**
     * @var \Magento\TestFramework\EventManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_eventManager = $this->getMock(
            'Magento\TestFramework\EventManager',
            ['fireEvent'],
            [[]]
        );
        $this->_object = new \Magento\TestFramework\Event\Magento($this->_eventManager);
    }

    protected function tearDown()
    {
        \Magento\TestFramework\Event\Magento::setDefaultEventManager(null);
    }

    public function testConstructorDefaultEventManager()
    {
        \Magento\TestFramework\Event\Magento::setDefaultEventManager($this->_eventManager);
        $this->_object = new \Magento\TestFramework\Event\Magento();
        $this->testInitStoreAfter();
    }

    /**
     * @dataProvider constructorExceptionDataProvider
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @param mixed $eventManager
     */
    public function testConstructorException($eventManager)
    {
        new \Magento\TestFramework\Event\Magento($eventManager);
    }

    public function constructorExceptionDataProvider()
    {
        return ['no event manager' => [null], 'not an event manager' => [new \stdClass()]];
    }

    public function testInitStoreAfter()
    {
        $this->_eventManager->expects($this->once())->method('fireEvent')->with('initStoreAfter');
        $this->_object->execute($this->getMock('\Magento\Framework\Event\Observer'));
    }
}
