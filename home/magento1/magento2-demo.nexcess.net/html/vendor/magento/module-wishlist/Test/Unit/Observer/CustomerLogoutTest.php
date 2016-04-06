<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Wishlist\Test\Unit\Observer;

use \Magento\Wishlist\Observer\CustomerLogout as Observer;

class CustomerLogoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Observer
     */
    protected $observer;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;


    public function setUp()
    {
        $this->customerSession = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods(['setWishlistItemCount', 'isLoggedIn', 'getCustomerId'])
            ->getMock();

        $this->observer = new Observer(
            $this->customerSession
        );
    }

    public function testExecute()
    {
        $event = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        /** @var $event \Magento\Framework\Event\Observer */

        $this->customerSession->expects($this->once())
            ->method('setWishlistItemCount')
            ->with($this->equalTo(0));

        $this->observer->execute($event);
    }
}
