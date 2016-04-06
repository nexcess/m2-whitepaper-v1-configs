<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Quote\Test\Unit\Model\Quote;

use \Magento\Quote\Model\Quote\Address;

use Magento\Store\Model\ScopeInterface;

/**
 * Test class for \Magento\Sales\Model\Order
 */
class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Address
     */
    private $address;

    /**
     * @var \Magento\Quote\Model\Quote | \PHPUnit_Framework_MockObject_MockObject
     */
    private $quote;

    /**
     * @var \Magento\Framework\App\Config | \PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfig;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config', [], [], '', false);

        $this->address = $objectManager->getObject(
                'Magento\Quote\Model\Quote\Address',
                [
                    'scopeConfig' => $this->scopeConfig
                ]
            );
        $this->quote = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);
        $this->address->setQuote($this->quote);
    }

    public function testValidateMiniumumAmountDisabled()
    {
        $storeId = 1;

        $this->quote->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('sales/minimum_order/active', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(false);

        $this->assertTrue($this->address->validateMinimumAmount());
    }

    public function testValidateMiniumumAmountVirtual()
    {
        $storeId = 1;
        $scopeConfigValues = [
            ['sales/minimum_order/active', ScopeInterface::SCOPE_STORE, $storeId, true],
            ['sales/minimum_order/amount', ScopeInterface::SCOPE_STORE, $storeId, 20],
            ['sales/minimum_order/tax_including', ScopeInterface::SCOPE_STORE, $storeId, true],
        ];

        $this->quote->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->quote->expects($this->once())
            ->method('getIsVirtual')
            ->willReturn(true);
        $this->address->setAddressType(Address::TYPE_SHIPPING);

        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->willReturnMap($scopeConfigValues);

        $this->assertTrue($this->address->validateMinimumAmount());
    }

    public function testValidateMiniumumAmount()
    {
        $storeId = 1;
        $scopeConfigValues = [
            ['sales/minimum_order/active', ScopeInterface::SCOPE_STORE, $storeId, true],
            ['sales/minimum_order/amount', ScopeInterface::SCOPE_STORE, $storeId, 20],
            ['sales/minimum_order/tax_including', ScopeInterface::SCOPE_STORE, $storeId, true],
        ];

        $this->quote->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->quote->expects($this->once())
            ->method('getIsVirtual')
            ->willReturn(false);

        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->willReturnMap($scopeConfigValues);

        $this->assertTrue($this->address->validateMinimumAmount());
    }

    public function testValidateMiniumumAmountNegative()
    {
        $storeId = 1;
        $scopeConfigValues = [
            ['sales/minimum_order/active', ScopeInterface::SCOPE_STORE, $storeId, true],
            ['sales/minimum_order/amount', ScopeInterface::SCOPE_STORE, $storeId, 20],
            ['sales/minimum_order/tax_including', ScopeInterface::SCOPE_STORE, $storeId, true],
        ];

        $this->quote->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->quote->expects($this->once())
            ->method('getIsVirtual')
            ->willReturn(false);
        $this->address->setAddressType(Address::TYPE_SHIPPING);

        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->willReturnMap($scopeConfigValues);

        $this->assertTrue($this->address->validateMinimumAmount());
    }
}
