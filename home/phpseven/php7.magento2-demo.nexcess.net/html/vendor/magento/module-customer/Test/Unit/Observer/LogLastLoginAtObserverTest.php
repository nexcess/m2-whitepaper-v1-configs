<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Observer;

use Magento\Customer\Model\Logger;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\Observer;
use Magento\Customer\Observer\LogLastLoginAtObserver;

/**
 * Class LogLastLoginAtObserverTest
 */
class LogLastLoginAtObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogLastLoginAtObserver
     */
    protected $logLastLoginAtObserver;

    /**
     * @var Logger | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->loggerMock = $this->getMock('Magento\Customer\Model\Logger', [], [], '', false);
        $this->logLastLoginAtObserver = new LogLastLoginAtObserver($this->loggerMock);
    }

    /**
     * @return void
     */
    public function testLogLastLoginAt()
    {
        $id = 1;

        $observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $eventMock = $this->getMock('Magento\Framework\Event', ['getCustomer'], [], '', false);
        $customerMock = $this->getMock('Magento\Customer\Model\Customer', [], [], '', false);

        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);
        $eventMock->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customerMock);
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($id);

        $this->loggerMock->expects($this->once())
            ->method('log');

        $this->logLastLoginAtObserver->execute($observerMock);
    }
}
