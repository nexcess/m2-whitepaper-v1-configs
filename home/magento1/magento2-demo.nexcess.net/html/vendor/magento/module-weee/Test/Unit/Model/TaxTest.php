<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Weee\Test\Unit\Model;

use Magento\Weee\Model\Tax;

/**
 * Class TaxTest
 */
class TaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Weee\Model\Tax
     */
    protected $model;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculationFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $accountManagement;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $weeeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrency;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceCollection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $data;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);


        $className = '\Magento\Framework\Model\Context';
        $this->context = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Framework\Registry';
        $this->registry = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Eav\Model\Entity\AttributeFactory';
        $this->attributeFactory = $this->getMock($className, ['create'], [], '', false);

        $className = '\Magento\Store\Model\StoreManagerInterface';
        $this->storeManager = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Tax\Model\CalculationFactory';
        $this->calculationFactory = $this->getMock($className, ['create'], [], '', false);

        $className = '\Magento\Customer\Model\Session';
        $this->customerSession = $this->getMock(
            $className,
            ['getCustomerId', 'getDefaultTaxShippingAddress', 'getDefaultTaxBillingAddress', 'getCustomerTaxClassId'],
            [],
            '',
            false
        );
        $this->customerSession->expects($this->any())->method('getCustomerId')->willReturn(null);
        $this->customerSession->expects($this->any())->method('getDefaultTaxShippingAddress')->willReturn(null);
        $this->customerSession->expects($this->any())->method('getDefaultTaxBillingAddress')->willReturn(null);
        $this->customerSession->expects($this->any())->method('getCustomerTaxClassId')->willReturn(null);

        $className = '\Magento\Customer\Api\AccountManagementInterface';
        $this->accountManagement = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Tax\Helper\Data';
        $this->taxData = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Weee\Model\ResourceModel\Tax';
        $this->resource = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Weee\Model\Config';
        $this->weeeConfig = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Framework\Pricing\PriceCurrencyInterface';
        $this->priceCurrency = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Framework\Data\Collection\AbstractDb';
        $this->resourceCollection = $this->getMock($className, [], [], '', false);

        $this->model = $this->objectManager->getObject(
            '\Magento\Weee\Model\Tax',
            [
                'context' => $this->context,
                'registry' => $this->registry,
                'attributeFactory' => $this->attributeFactory,
                'storeManager' => $this->storeManager,
                'calculationFactory' => $this->calculationFactory,
                'customerSession' => $this->customerSession,
                'accountManagement' => $this->accountManagement,
                'taxData' => $this->taxData,
                'resource' => $this->resource,
                'weeeConfig' => $this->weeeConfig,
                'priceCurrency' => $this->priceCurrency,
                'resourceCollection' => $this->resourceCollection,
            ]
        );
    }
    /**
     * test GetProductWeeeAttributes
     */
    public function testGetProductWeeeAttributes()
    {
        $product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $website = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $store = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $group = $this->getMock('\Magento\Store\Model\Group', [], [], '', false);

        $attribute = $this->getMock('\Magento\Eav\Model\Entity\Attribute', [], [], '', false);
        $calculation = $this->getMock('Magento\Tax\Model\Calculation', [], [], '', false);

        $obj = new \Magento\Framework\DataObject(['country' => 'US', 'region' => 'TX']);
        $calculation->expects($this->once())
            ->method('getRateRequest')
            ->willReturn($obj);
        $calculation->expects($this->once())
            ->method('getDefaultRateRequest')
            ->willReturn($obj);
        $calculation->expects($this->any())
            ->method('getRate')
            ->willReturn('10');

        $attribute->expects($this->once())
            ->method('getAttributeCodesByFrontendType')
            ->willReturn(['0'=>'fpt']);

        $store->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $product->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $website->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $website->expects($this->any())
            ->method('getDefaultGroup')
            ->willReturn($group);

        $group->expects($this->any())
            ->method('getDefaultStore')
            ->willReturn($store);

        $this->storeManager->expects($this->any())
            ->method('getWebsite')
            ->willReturn($website);

        $this->weeeConfig->expects($this->any())
            ->method('isEnabled')
            ->willReturn(true);

        $this->weeeConfig->expects($this->any())
            ->method('isTaxable')
            ->willReturn(true);

        $this->attributeFactory->expects($this->any())
            ->method('create')
            ->willReturn($attribute);

        $this->calculationFactory->expects($this->any())
            ->method('create')
            ->willReturn($calculation);

        $this->priceCurrency->expects($this->any())
            ->method('round')
            ->with(0.1)
            ->willReturn(0.25);

        $this->resource->expects($this->any())
            ->method('fetchWeeeTaxCalculationsByEntity')
            ->willReturn([
                0 => [
                    'weee_value' => 1,
                    'label_value' => 'fpt_label',
                    'attribute_code' => 'fpt_code',
                    ]
            ]);

        $result = $this->model->getProductWeeeAttributes($product, null, null, null, true);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey(0, $result);
        $obj = $result[0];
        $this->assertEquals(1, $obj->getAmount());
        $this->assertEquals(0.25, $obj->getTaxAmount());
        $this->assertEquals('fpt_code', $obj->getCode());
    }
}
