<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tax\Test\Unit\Setup;

class TaxSetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tax\Setup\TaxSetup
     */
    protected $taxSetup;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeConfigMock;

    protected function setUp()
    {
        $this->typeConfigMock = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');

        $salesSetup = $this->getMock('\Magento\Sales\Setup\SalesSetup', [], [], '', false);
        $salesSetupFactory = $this->getMock('Magento\Sales\Setup\SalesSetupFactory', ['create'], [], '', false);
        $salesSetupFactory->expects($this->any())->method('create')->will($this->returnValue($salesSetup));

        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->taxSetup = $helper->getObject(
            'Magento\Tax\Setup\TaxSetup',
            [
                'productTypeConfig' => $this->typeConfigMock,
                'salesSetupFactory' => $salesSetupFactory,
            ]
        );
    }

    public function testGetTaxableItems()
    {
        $refundable = ['simple', 'simple2'];
        $this->typeConfigMock->expects(
            $this->once()
        )->method(
            'filter'
        )->with(
            'taxable'
        )->will(
            $this->returnValue($refundable)
        );
        $this->assertEquals($refundable, $this->taxSetup->getTaxableItems());
    }
}
