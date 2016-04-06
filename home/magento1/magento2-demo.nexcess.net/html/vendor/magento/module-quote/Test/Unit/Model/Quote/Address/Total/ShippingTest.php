<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Quote\Test\Unit\Model\Quote\Address\Total;

class ShippingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Quote\Model\Quote\Address\Total\Shipping
     */
    protected $shippingModel;

    /** @var \Magento\Quote\Model\Quote|\PHPUnit_Framework_MockObject_MockObject */
    protected $quote;

    /** @var \Magento\Quote\Model\Quote\Address\Total|\PHPUnit_Framework_MockObject_MockObject  */
    protected $total;

    /** @var \Magento\Quote\Api\Data\ShippingAssignmentInterface|\PHPUnit_Framework_MockObject_MockObject  */
    protected $shippingAssignment;

    /** @var \Magento\Quote\Model\Quote\Address|\PHPUnit_Framework_MockObject_MockObject  */
    protected $address;

    /** @var \Magento\Quote\Api\Data\ShippingInterface|\PHPUnit_Framework_MockObject_MockObject  */
    protected $shipping;

    /** @var \Magento\Quote\Model\Quote\Address\FreeShippingInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $freeShipping;

    /** @var \Magento\Quote\Api\Data\CartItemInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $cartItem;

    /** @var \Magento\Quote\Model\Quote\Address\Rate|\PHPUnit_Framework_MockObject_MockObject */
    protected $rate;

    /** @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject */
    protected $store;

    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $priceCurrency;

    protected function setUp()
    {
        $this->freeShipping = $this->getMockForAbstractClass(
            'Magento\Quote\Model\Quote\Address\FreeShippingInterface',
            [],
            '',
            false
        );
        $this->priceCurrency = $this->getMockForAbstractClass(
            'Magento\Framework\Pricing\PriceCurrencyInterface',
            [],
            '',
            false
        );
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->shippingModel = $objectManager->getObject(
            'Magento\Quote\Model\Quote\Address\Total\Shipping',
            [
                'freeShipping' => $this->freeShipping,
                'priceCurrency' => $this->priceCurrency,
            ]
        );

        $this->quote = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);
        $this->total = $this->getMock(
            'Magento\Quote\Model\Quote\Address\Total',
            [
                'setShippingAmount',
                'setBaseShippingAmount',
                'setBaseTotalAmount',
                'setTotalAmount',
                'setShippingDescription',
            ],
            [],
            '',
            false
        );
        $this->shippingAssignment = $this->getMockForAbstractClass(
            'Magento\Quote\Api\Data\ShippingAssignmentInterface',
            [],
            '',
            false
        );
        $this->address = $this->getMock(
            'Magento\Quote\Model\Quote\Address',
            [
                'setWeight',
                'setFreeMethodWeight',
                'getWeight',
                'getFreeMethodWeight',
                'setFreeShipping',
                'setItemQty',
                'collectShippingRates',
                'getAllShippingRates',
                'setShippingDescription',
                'getShippingDescription',
                'getFreeShipping',
            ],
            [],
            '',
            false
        );
        $this->shipping = $this->getMockForAbstractClass('Magento\Quote\Api\Data\ShippingInterface', [], '', false);
        $this->cartItem = $this->getMockForAbstractClass(
            'Magento\Quote\Api\Data\CartItemInterface',
            [],
            '',
            false,
            false,
            true,
            [
                'getFreeShipping',
                'getProduct',
                'getParentItem',
                'getHasChildren',
                'isVirtual',
                'getWeight',
                'getQty',
                'setRowWeight',
            ]
        );
        $this->rate = $this->getMock(
            'Magento\Quote\Model\Quote\Address\Rate',
            ['getPrice', 'getCode', 'getCarrierTitle', 'getMethodTitle'],
            [],
            '',
            false
        );
        $this->store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
    }

    public function testFetch()
    {
        $shippingAmount = 100;
        $shippingDescription = 100;
        $expectedResult = [
            'code' => 'shipping',
            'value' => 100,
            'title' => __('Shipping & Handling (%1)', $shippingDescription)
        ];

        $quoteMock = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false);
        $totalMock = $this->getMock(
            '\Magento\Quote\Model\Quote\Address\Total',
            ['getShippingAmount', 'getShippingDescription'],
            [],
            '',
            false
        );

        $totalMock->expects($this->once())->method('getShippingAmount')->willReturn($shippingAmount);
        $totalMock->expects($this->once())->method('getShippingDescription')->willReturn($shippingDescription);
        $this->assertEquals($expectedResult, $this->shippingModel->fetch($quoteMock, $totalMock));
    }

    public function testCollect()
    {
        $this->shippingAssignment->expects($this->exactly(3))
            ->method('getShipping')
            ->willReturn($this->shipping);
        $this->shipping->expects($this->exactly(2))
            ->method('getAddress')
            ->willReturn($this->address);
        $this->shipping->expects($this->once())
            ->method('getMethod')
            ->willReturn('flatrate');
        $this->shippingAssignment->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn([$this->cartItem]);
        $this->freeShipping->expects($this->once())
            ->method('isFreeShipping')
            ->with($this->quote, [$this->cartItem])
            ->willReturn(true);
        $this->address->expects($this->atLeastOnce())
            ->method('setFreeShipping');
        $this->total->expects($this->atLeastOnce())
            ->method('setTotalAmount');
        $this->total->expects($this->atLeastOnce())
            ->method('setBaseTotalAmount');
        $this->cartItem->expects($this->atLeastOnce())
            ->method('getProduct')
            ->willReturnSelf();
        $this->cartItem->expects($this->atLeastOnce())
            ->method('isVirtual')
            ->willReturn(false);
        $this->cartItem->expects($this->once())
            ->method('getParentItem')
            ->willReturn(false);
        $this->cartItem->expects($this->once())
            ->method('getHasChildren')
            ->willReturn(false);
        $this->cartItem->expects($this->once())
            ->method('getWeight')
            ->willReturn(2);
        $this->cartItem->expects($this->atLeastOnce())
            ->method('getQty')
            ->willReturn(2);
        $this->address->expects($this->atLeastOnce())
            ->method('getFreeShipping')
            ->willReturn(true);
        $this->cartItem->expects($this->once())
            ->method('setRowWeight')
            ->with(0);
        $this->address->expects($this->once())
            ->method('setItemQty')
            ->with(2);
        $this->address->expects($this->atLeastOnce())
            ->method('setWeight');
        $this->address->expects($this->atLeastOnce())
            ->method('setFreeMethodWeight');
        $this->address->expects($this->once())
            ->method('collectShippingRates');
        $this->address->expects($this->once())
            ->method('getAllShippingRates')
            ->willReturn([$this->rate]);
        $this->rate->expects($this->once())
            ->method('getCode')
            ->willReturn('flatrate');
        $this->quote->expects($this->once())
            ->method('getStore')
            ->willReturn($this->store);
        $this->rate->expects($this->atLeastOnce())
            ->method('getPrice')
            ->willReturn(5);
        $this->priceCurrency->expects($this->once())
            ->method('convert')
            ->with(5, $this->store)
            ->willReturn(10);
        $this->total->expects($this->once())
            ->method('setShippingAmount')
            ->with(10);
        $this->total->expects($this->once())
            ->method('setBaseShippingAmount')
            ->with(5);
        $this->rate->expects($this->once())
            ->method('getCarrierTitle')
            ->willReturn('Carrier title');
        $this->rate->expects($this->once())
            ->method('getMethodTitle')
            ->willReturn('Method title');
        $this->address->expects($this->once())
            ->method('setShippingDescription')
            ->with('Carrier title - Method title');
        $this->address->expects($this->once())
            ->method('getShippingDescription')
            ->willReturn('Carrier title - Method title');
        $this->total->expects($this->once())
            ->method('setShippingDescription')
            ->with('Carrier title - Method title');

        $this->shippingModel->collect($this->quote, $this->shippingAssignment, $this->total);
    }
}
