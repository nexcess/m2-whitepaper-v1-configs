<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payment\Test\Unit\Gateway\Data;

use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Data\Order\OrderAdapter;
use Magento\Sales\Model\Order;

/**
 * Class PaymentDataObjectFactoryTest
 */
class PaymentDataObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var PaymentDataObjectFactory */
    protected $model;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Payment\Gateway\Data\Order\OrderAdapterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderAdapterFactoryMock;

    /**
     * @var \Magento\Payment\Gateway\Data\Quote\QuoteAdapterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAdapterFactoryMock;

    /**
     * @var \Magento\Payment\Gateway\Data\PaymentDataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentDataObjectMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->getMockForAbstractClass();

        $this->orderAdapterFactoryMock =
            $this->getMockBuilder('Magento\Payment\Gateway\Data\Order\OrderAdapterFactory')
                ->setMethods(['create'])
                ->disableOriginalConstructor()
                ->getMock();

        $this->quoteAdapterFactoryMock =
            $this->getMockBuilder('Magento\Payment\Gateway\Data\Quote\QuoteAdapterFactory')
                ->setMethods(['create'])
                ->disableOriginalConstructor()
                ->getMock();

        $this->paymentDataObjectMock =
            $this->getMock('Magento\Payment\Gateway\Data\PaymentDataObjectInterface');

        $this->model = new PaymentDataObjectFactory(
            $this->objectManagerMock,
            $this->orderAdapterFactoryMock,
            $this->quoteAdapterFactoryMock
        );
    }

    public function testCreatePaymentDataObjectFromOrder()
    {
        /** @var Order $orderMock */
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var OrderAdapter $orderAdapterMock */
        $orderAdapterMock = $this->getMockBuilder('Magento\Payment\Gateway\Data\Order\OrderAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Sales\Model\Order\Payment $paymentInfoMock */
        $paymentInfoMock = $this->getMockBuilder('Magento\Sales\Model\Order\Payment')
            ->disableOriginalConstructor()
            ->getMock();

        $paymentInfoMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $this->orderAdapterFactoryMock->expects($this->once())
            ->method('create')
            ->with(['order' => $orderMock])
            ->willReturn($orderAdapterMock);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                'Magento\Payment\Gateway\Data\PaymentDataObject',
                [
                    'order' => $orderAdapterMock,
                    'payment' => $paymentInfoMock
                ]
            )->willReturn($this->paymentDataObjectMock);

        $this->assertSame($this->paymentDataObjectMock, $this->model->create($paymentInfoMock));
    }

    public function testCreatePaymentDataObjectFromQuote()
    {
        /** @var \Magento\Quote\Model\Quote $quoteMock */
        $quoteMock = $this->getMockBuilder('Magento\Quote\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var OrderAdapter $orderAdapterMock */
        $quoteAdapterMock = $this->getMockBuilder('Magento\Payment\Gateway\Data\Quote\QuoteAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Quote\Model\Quote\Payment $paymentInfoMock */
        $paymentInfoMock = $this->getMockBuilder('Magento\Quote\Model\Quote\Payment')
            ->disableOriginalConstructor()
            ->getMock();

        $paymentInfoMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $this->quoteAdapterFactoryMock->expects($this->once())
            ->method('create')
            ->with(['quote' => $quoteMock])
            ->willReturn($quoteAdapterMock);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                'Magento\Payment\Gateway\Data\PaymentDataObject',
                [
                    'order' => $quoteAdapterMock,
                    'payment' => $paymentInfoMock
                ]
            )->willReturn($this->paymentDataObjectMock);

        $this->assertSame($this->paymentDataObjectMock, $this->model->create($paymentInfoMock));
    }
}
