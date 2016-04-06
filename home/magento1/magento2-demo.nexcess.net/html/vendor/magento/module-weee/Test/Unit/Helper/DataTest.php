<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Weee\Test\Unit\Helper;

use Magento\Weee\Helper\Data as WeeeHelper;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    const ROW_AMOUNT_INVOICED = '200';
    const BASE_ROW_AMOUNT_INVOICED = '400';
    const TAX_AMOUNT_INVOICED = '20';
    const BASE_TAX_AMOUNT_INVOICED = '40';
    const ROW_AMOUNT_REFUNDED = '100';
    const BASE_ROW_AMOUNT_REFUNDED = '201';
    const TAX_AMOUNT_REFUNDED = '10';
    const BASE_TAX_AMOUNT_REFUNDED = '21';

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Weee\Model\Tax
     */
    protected $weeeTax;

    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $helperData;

    protected function setUp()
    {
        $this->product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $weeeConfig = $this->getMock('Magento\Weee\Model\Config', [], [], '', false);
        $weeeConfig->expects($this->any())->method('isEnabled')->will($this->returnValue(true));
        $weeeConfig->expects($this->any())->method('getListPriceDisplayType')->will($this->returnValue(1));
        $this->weeeTax = $this->getMock('Magento\Weee\Model\Tax', [], [], '', false);
        $this->weeeTax->expects($this->any())->method('getWeeeAmount')->will($this->returnValue('11.26'));
        $arguments = [
            'weeeConfig' => $weeeConfig,
            'weeeTax' => $this->weeeTax,
        ];
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->helperData = $helper->getObject('Magento\Weee\Helper\Data', $arguments);
    }

    public function testGetAmount()
    {
        $this->product->expects($this->any())->method('hasData')->will($this->returnValue(false));
        $this->product->expects($this->any())->method('getData')->will($this->returnValue(11.26));

        $this->assertEquals('11.26', $this->helperData->getAmountExclTax($this->product));
    }

    /**
     * @return \Magento\Sales\Model\Order\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    private function setupOrderItem()
    {
        $orderItem = $this->getMockBuilder('\Magento\Sales\Model\Order\Item')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();

        $orderItem->setData(
            'weee_tax_applied',
            \Zend_Json::encode(
                [
                    [
                        WeeeHelper::KEY_WEEE_AMOUNT_INVOICED => self::ROW_AMOUNT_INVOICED,
                        WeeeHelper::KEY_BASE_WEEE_AMOUNT_INVOICED => self::BASE_ROW_AMOUNT_INVOICED,
                        WeeeHelper::KEY_WEEE_TAX_AMOUNT_INVOICED => self::TAX_AMOUNT_INVOICED,
                        WeeeHelper::KEY_BASE_WEEE_TAX_AMOUNT_INVOICED => self::BASE_TAX_AMOUNT_INVOICED,
                        WeeeHelper::KEY_WEEE_AMOUNT_REFUNDED => self::ROW_AMOUNT_REFUNDED,
                        WeeeHelper::KEY_BASE_WEEE_AMOUNT_REFUNDED => self::BASE_ROW_AMOUNT_REFUNDED,
                        WeeeHelper::KEY_WEEE_TAX_AMOUNT_REFUNDED => self::TAX_AMOUNT_REFUNDED,
                        WeeeHelper::KEY_BASE_WEEE_TAX_AMOUNT_REFUNDED => self::BASE_TAX_AMOUNT_REFUNDED,
                    ],
                    [
                        WeeeHelper::KEY_WEEE_AMOUNT_INVOICED => self::ROW_AMOUNT_INVOICED,
                        WeeeHelper::KEY_BASE_WEEE_AMOUNT_INVOICED => self::BASE_ROW_AMOUNT_INVOICED,
                        WeeeHelper::KEY_WEEE_TAX_AMOUNT_INVOICED => self::TAX_AMOUNT_INVOICED,
                        WeeeHelper::KEY_BASE_WEEE_TAX_AMOUNT_INVOICED => self::BASE_TAX_AMOUNT_INVOICED,
                        WeeeHelper::KEY_WEEE_AMOUNT_REFUNDED => self::ROW_AMOUNT_REFUNDED,
                        WeeeHelper::KEY_BASE_WEEE_AMOUNT_REFUNDED => self::BASE_ROW_AMOUNT_REFUNDED,
                        WeeeHelper::KEY_WEEE_TAX_AMOUNT_REFUNDED => self::TAX_AMOUNT_REFUNDED,
                        WeeeHelper::KEY_BASE_WEEE_TAX_AMOUNT_REFUNDED => self::BASE_TAX_AMOUNT_REFUNDED,
                    ],
                ]
            )
        );
        return $orderItem;
    }

    public function testGetWeeeAmountInvoiced()
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getWeeeAmountInvoiced($orderItem);
        $this->assertEquals(self::ROW_AMOUNT_INVOICED, $value);
    }

    public function testGetBaseWeeeAmountInvoiced()
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getBaseWeeeAmountInvoiced($orderItem);
        $this->assertEquals(self::BASE_ROW_AMOUNT_INVOICED, $value);
    }

    public function testGetWeeeTaxAmountInvoiced()
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getWeeeTaxAmountInvoiced($orderItem);
        $this->assertEquals(self::TAX_AMOUNT_INVOICED, $value);
    }

    public function testGetWeeeBaseTaxAmountInvoiced()
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getBaseWeeeTaxAmountInvoiced($orderItem);
        $this->assertEquals(self::BASE_TAX_AMOUNT_INVOICED, $value);
    }

    public function testGetWeeeAmountRefunded()
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getWeeeAmountRefunded($orderItem);
        $this->assertEquals(self::ROW_AMOUNT_REFUNDED, $value);
    }

    public function testGetBaseWeeeAmountRefunded()
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getBaseWeeeAmountRefunded($orderItem);
        $this->assertEquals(self::BASE_ROW_AMOUNT_REFUNDED, $value);
    }

    public function testGetWeeeTaxAmountRefunded()
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getWeeeTaxAmountRefunded($orderItem);
        $this->assertEquals(self::TAX_AMOUNT_REFUNDED, $value);
    }

    public function testGetBaseWeeeTaxAmountRefunded()
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getBaseWeeeTaxAmountRefunded($orderItem);
        $this->assertEquals(self::BASE_TAX_AMOUNT_REFUNDED, $value);
    }

    public function testGetWeeeAttributesForBundle()
    {
        $prodId1 = 1;
        $prodId2 = 2;
        $fptCode1 = 'fpt' . $prodId1;
        $fptCode2 = 'fpt' . $prodId2;

        $weeeObject1 = new \Magento\Framework\DataObject(
            [
                'code' => $fptCode1,
                'amount' => '15.0000',
            ]
        );

        $weeeObject2 = new \Magento\Framework\DataObject(
            [
                'code' => $fptCode2,
                'amount' => '15.0000',
            ]
        );

        $testArray = [$prodId1 => [$fptCode1 => $weeeObject1], $prodId2 => [$fptCode2 => $weeeObject2]];

        $this->weeeTax->expects($this->any())
            ->method('getProductWeeeAttributes')
            ->will($this->returnValue([$weeeObject1, $weeeObject2]));

        $productSimple = $this->getMock('\Magento\Catalog\Model\Product\Type\Simple', ['getId'], [], '', false);

        $productSimple->expects($this->at(0))
            ->method('getId')
            ->will($this->returnValue($prodId1));
        $productSimple->expects($this->at(1))
            ->method('getId')
            ->will($this->returnValue($prodId2));

        $productInstance = $this->getMock('\Magento\Bundle\Model\Product\Type', [], [], '', false);
        $productInstance->expects($this->any())
            ->method('getSelectionsCollection')
            ->will($this->returnValue([$productSimple]));

        $store=$this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->getMock(
            '\Magento\Bundle\Model\Product',
            ['getTypeInstance', 'getStoreId', 'getStore', 'getTypeId'],
            [],
            '',
            false
        );
        $product->expects($this->any())
            ->method('getTypeInstance')
            ->will($this->returnValue($productInstance));

        $product->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue(1));

        $product->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $product->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue('bundle'));

        $registry=$this->getMock('Magento\Framework\Registry', [], [], '', false);
        $registry->expects($this->any())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($product));
        
        $this->assertEquals($testArray, $this->helperData->getWeeeAttributesForBundle($product));
    }

    public function testGetAppliedSimple()
    {
        $testArray = ['key' => 'value'];
        $itemProductSimple=$this->getMock('\Magento\Quote\Model\Quote\Item', ['getWeeeTaxApplied'], [], '', false);
        $itemProductSimple->expects($this->any())
            ->method('getHasChildren')
            ->will($this->returnValue(false));

        $itemProductSimple->expects($this->any())
            ->method('getWeeeTaxApplied')
            ->will($this->returnValue(\Zend_Json::encode($testArray)));

        $this->assertEquals($testArray, $this->helperData->getApplied($itemProductSimple));
    }

    public function testGetAppliedBundle()
    {
        $testArray1 = ['key1' => 'value1'];
        $testArray2 = ['key2' => 'value2'];

        $testArray = array_merge($testArray1, $testArray2);

        $itemProductSimple1=$this->getMock('\Magento\Quote\Model\Quote\Item', ['getWeeeTaxApplied'], [], '', false);
        $itemProductSimple2=$this->getMock('\Magento\Quote\Model\Quote\Item', ['getWeeeTaxApplied'], [], '', false);

        $itemProductSimple1->expects($this->any())
            ->method('getWeeeTaxApplied')
            ->will($this->returnValue(\Zend_Json::encode($testArray1)));

        $itemProductSimple2->expects($this->any())
            ->method('getWeeeTaxApplied')
            ->will($this->returnValue(\Zend_Json::encode($testArray2)));

        $itemProductBundle=$this->getMock(
            '\Magento\Quote\Model\Quote\Item',
            ['getHasChildren', 'isChildrenCalculated', 'getChildren'],
            [],
            '',
            false
        );
        $itemProductBundle->expects($this->any())
            ->method('getHasChildren')
            ->will($this->returnValue(true));
        $itemProductBundle->expects($this->any())
            ->method('isChildrenCalculated')
            ->will($this->returnValue(true));
        $itemProductBundle->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue([$itemProductSimple1, $itemProductSimple2]));

        $this->assertEquals($testArray, $this->helperData->getApplied($itemProductBundle));
    }

    public function testGetRecursiveAmountSimple()
    {
        $testAmountUnit = 2;
        $testAmountRow = 34;

        $itemProductSimple=$this->getMock(
            '\Magento\Quote\Model\Quote\Item',
            ['getWeeeTaxAppliedAmount', 'getWeeeTaxAppliedRowAmount'],
            [],
            '',
            false
        );
        $itemProductSimple->expects($this->any())
            ->method('getHasChildren')
            ->will($this->returnValue(false));

        $itemProductSimple->expects($this->any())
            ->method('getWeeeTaxAppliedAmount')
            ->will($this->returnValue($testAmountUnit));
        $itemProductSimple->expects($this->any())
            ->method('getWeeeTaxAppliedRowAmount')
            ->will($this->returnValue($testAmountRow));

        $this->assertEquals($testAmountUnit, $this->helperData->getWeeeTaxAppliedAmount($itemProductSimple));
        $this->assertEquals($testAmountRow, $this->helperData->getWeeeTaxAppliedRowAmount($itemProductSimple));
    }

    public function testGetRecursiveAmountBundle()
    {
        $testAmountUnit1 = 1;
        $testAmountUnit2 = 2;
        $testTotalUnit = $testAmountUnit1 + $testAmountUnit2;

        $testAmountRow1 = 33;
        $testAmountRow2 = 444;
        $testTotalRow = $testAmountRow1 + $testAmountRow2;

        $itemProductSimple1=$this->getMock(
            '\Magento\Quote\Model\Quote\Item',
            ['getWeeeTaxAppliedAmount', 'getWeeeTaxAppliedRowAmount'],
            [],
            '',
            false
        );
        $itemProductSimple2=$this->getMock(
            '\Magento\Quote\Model\Quote\Item',
            ['getWeeeTaxAppliedAmount', 'getWeeeTaxAppliedRowAmount'],
            [],
            '',
            false
        );

        $itemProductSimple1->expects($this->any())
            ->method('getWeeeTaxAppliedAmount')
            ->will($this->returnValue($testAmountUnit1));
        $itemProductSimple1->expects($this->any())
            ->method('getWeeeTaxAppliedRowAmount')
            ->will($this->returnValue($testAmountRow1));

        $itemProductSimple2->expects($this->any())
            ->method('getWeeeTaxAppliedAmount')
            ->will($this->returnValue($testAmountUnit2));
        $itemProductSimple2->expects($this->any())
            ->method('getWeeeTaxAppliedRowAmount')
            ->will($this->returnValue($testAmountRow2));

        $itemProductBundle=$this->getMock(
            '\Magento\Quote\Model\Quote\Item',
            ['getHasChildren', 'isChildrenCalculated', 'getChildren'],
            [],
            '',
            false
        );
        $itemProductBundle->expects($this->any())
            ->method('getHasChildren')
            ->will($this->returnValue(true));
        $itemProductBundle->expects($this->any())
            ->method('isChildrenCalculated')
            ->will($this->returnValue(true));
        $itemProductBundle->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue([$itemProductSimple1, $itemProductSimple2]));

        $this->assertEquals($testTotalUnit, $this->helperData->getWeeeTaxAppliedAmount($itemProductBundle));
        $this->assertEquals($testTotalRow, $this->helperData->getWeeeTaxAppliedRowAmount($itemProductBundle));
    }

    public function testGetProductWeeeAttributesForDisplay()
    {
        $store = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->product->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $this->helperData->getProductWeeeAttributesForDisplay($this->product);
    }

    public function testGetTaxDisplayConfig()
    {
        $expected = 1;
        $taxData = $this->getMock('\Magento\Tax\Helper\Data', ['getPriceDisplayType'], [], '', false);
        $taxData->expects($this->any())->method('getPriceDisplayType')->will($this->returnValue($expected));
        $arguments = [
            'taxData' => $taxData,
        ];
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $helperData = $helper->getObject('Magento\Weee\Helper\Data', $arguments);

        $this->assertEquals($expected, $helperData->getTaxDisplayConfig());
    }
}
