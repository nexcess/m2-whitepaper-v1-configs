<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Payment\Test\Unit\Model\Method;

class FreeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Payment\Model\Method\Free */
    protected $methodFree;

    /**  @var \PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfig;

    /**  @var \PHPUnit_Framework_MockObject_MockObject */
    protected $currencyPrice;

    protected function setUp()
    {
        $paymentData  = $this->getMock('Magento\Payment\Helper\Data', [], [], '', false);
        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);
        $this->currencyPrice = $this->getMockBuilder('Magento\Framework\Pricing\PriceCurrencyInterface')->getMock();

        $context = $this->getMock('\Magento\Framework\Model\Context', ['getEventDispatcher'], [], '', false);
        $eventManagerMock = $this->getMock('\Magento\Framework\Event\ManagerInterface');
        $context->expects($this->any())->method('getEventDispatcher')->willReturn($eventManagerMock);

        $registry = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $extensionAttributesFactory = $this->getMock(
            'Magento\Framework\Api\ExtensionAttributesFactory',
            [],
            [],
            '',
            false
        );
        $customAttributeFactory = $this->getMock('\Magento\Framework\Api\AttributeValueFactory', [], [], '', false);

        $loggerMock = $this->getMockBuilder('\Magento\Payment\Model\Method\Logger')
            ->setConstructorArgs([$this->getMockForAbstractClass('Psr\Log\LoggerInterface')])
            ->getMock();

        $this->methodFree = new \Magento\Payment\Model\Method\Free(
            $context,
            $registry,
            $extensionAttributesFactory,
            $customAttributeFactory,
            $paymentData,
            $this->scopeConfig,
            $loggerMock,
            $this->currencyPrice
        );
    }

    /**
     * @param string $orderStatus
     * @param string $paymentAction
     * @param mixed $result
     * @dataProvider getConfigPaymentActionProvider
     */
    public function testGetConfigPaymentAction($orderStatus, $paymentAction, $result)
    {
        $this->scopeConfig->expects($this->at(0))
            ->method('getValue')
            ->will($this->returnValue($orderStatus));

        if ($orderStatus != 'pending') {
            $this->scopeConfig->expects($this->at(1))
                ->method('getValue')
                ->will($this->returnValue($paymentAction));
        }
        $this->assertEquals($result, $this->methodFree->getConfigPaymentAction());
    }

    /**
     * @param float $grandTotal
     * @param bool $isActive
     * @param bool $notEmptyQuote
     * @param bool $result
     * @dataProvider getIsAvailableProvider
     */
    public function testIsAvailable($grandTotal, $isActive, $notEmptyQuote, $result)
    {
        $quote = null;
        if ($notEmptyQuote) {
            $quote = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);
            $quote->expects($this->any())
                ->method('__call')
                ->with($this->equalTo('getGrandTotal'))
                ->will($this->returnValue($grandTotal));
        }

        $this->currencyPrice->expects($this->any())
            ->method('round')
            ->willReturnArgument(0);

        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($isActive));

        $this->assertEquals($result, $this->methodFree->isAvailable($quote));
    }

    /**
     * @return array
     */
    public function getIsAvailableProvider()
    {
        return [
            [0, true, true, true],
            [0.1, true, true, false],
            [0, false, false, false],
            [1, true, false, false],
            [0, true, false, false]
        ];
    }

    /**
     * @return array
     */
    public function getConfigPaymentActionProvider()
    {
        return [
            ['pending', 'action', null],
            ['processing', 'payment_action', 'payment_action']
        ];
    }
}
