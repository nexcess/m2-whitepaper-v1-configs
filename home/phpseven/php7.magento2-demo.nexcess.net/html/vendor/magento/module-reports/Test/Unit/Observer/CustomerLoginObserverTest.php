<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reports\Test\Unit\Observer;

class CustomerLoginObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reports\Observer\CustomerLoginObserver
     */
    protected $observer;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Customer\Model\Visitor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerVisitorMock;

    /**
     * @var \Magento\Reports\Model\Product\Index\Viewed|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productIndexMock;

    /**
     * @var \Magento\Reports\Model\Event|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportEventMock;

    /**
     * @var \Magento\Reports\Model\Product\Index\ComparedFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productCompFactoryMock;

    /**
     * @var \Magento\Reports\Model\Product\Index\Compared|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productCompModelMock;

    /**
     * @var \Magento\Reports\Model\Product\Index\ViewedFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productIndexFactoryMock;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->customerSessionMock = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()->getMock();
        $this->customerVisitorMock = $this->getMockBuilder('Magento\Customer\Model\Visitor')
            ->disableOriginalConstructor()->getMock();

        $this->productIndexFactoryMock = $this->getMockBuilder('Magento\Reports\Model\Product\Index\ViewedFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()->getMock();
        $this->productIndexMock = $this->getMockBuilder('Magento\Reports\Model\Product\Index\Viewed')
            ->disableOriginalConstructor()->getMock();

        $this->productIndexFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->productIndexMock);

        $reportEventFactory = $this->getMockBuilder('Magento\Reports\Model\EventFactory')
            ->setMethods(['create'])->disableOriginalConstructor()->getMock();
        $this->reportEventMock = $this->getMockBuilder('Magento\Reports\Model\Event')
            ->disableOriginalConstructor()->getMock();

        $reportEventFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->reportEventMock);

        $this->productCompModelMock = $this->getMockBuilder('Magento\Reports\Model\Product\Index\Compared')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productCompFactoryMock = $this->getMockBuilder('Magento\Reports\Model\Product\Index\ComparedFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->productCompFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->productCompModelMock);

        $this->observer = $objectManager->getObject(
            'Magento\Reports\Observer\CustomerLoginObserver',
            [
                'event' => $reportEventFactory,
                'productCompFactory' => $this->productCompFactoryMock,
                'productIndexFactory' => $this->productIndexFactoryMock,
                'customerSession' => $this->customerSessionMock,
                'customerVisitor' => $this->customerVisitorMock,
            ]
        );
    }



    /**
     * @return void
     */
    public function testCustomerLoginLoggedInTrue()
    {
        $customerId = 222;
        $visitorId = 333;
        $observerMock = $this->getObserverMock(111);

        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->willReturn(true);
        $this->customerSessionMock->expects($this->once())->method('getCustomerId')->willReturn($customerId);

        $this->customerVisitorMock->expects($this->once())->method('getId')->willReturn($visitorId);

        $this->reportEventMock->expects($this->once())->method('updateCustomerType')->with($visitorId, $customerId);

        $this->productCompModelMock->expects($this->once())->method('updateCustomerFromVisitor')->willReturnSelf();
        $this->productCompModelMock->expects($this->once())->method('calculate')->willReturnSelf();

        $this->productIndexMock->expects($this->once())->method('updateCustomerFromVisitor')->willReturnSelf();
        $this->productIndexMock->expects($this->once())->method('calculate')->willReturnSelf();

        $this->observer->execute($observerMock);
    }

    /**
     * @return void
     */
    public function testCustomerLoginLoggedInFalse()
    {
        $observerMock = $this->getObserverMock(111);

        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->willReturn(false);
        $this->customerSessionMock->expects($this->never())->method('getCustomerId');

        $this->customerVisitorMock->expects($this->never())->method('getId');

        $this->productCompModelMock->expects($this->never())->method('updateCustomerFromVisitor')->willReturnSelf();
        $this->productCompModelMock->expects($this->never())->method('calculate')->willReturnSelf();

        $this->productIndexMock->expects($this->never())->method('updateCustomerFromVisitor')->willReturnSelf();
        $this->productIndexMock->expects($this->never())->method('calculate')->willReturnSelf();

        $this->observer->execute($observerMock);
    }

    /**
     * @param int $productId
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getObserverMock($productId)
    {
        $eventObserverMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock = $this->getMockBuilder('Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getProduct'])->getMock();
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->any())->method('getId')->willReturn($productId);

        $eventMock->expects($this->any())->method('getProduct')->willReturn($productMock);

        $eventObserverMock->expects($this->any())->method('getEvent')->willReturn($eventMock);

        return $eventObserverMock;
    }
}
