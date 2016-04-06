<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ups\Test\Unit\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;

class CarrierTest extends \PHPUnit_Framework_TestCase
{
    const FREE_METHOD_NAME = 'free_method';

    const PAID_METHOD_NAME = 'paid_method';

    /**
     * Model under test
     *
     * @var \Magento\Quote\Model\Quote\Address\RateResult\Error|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $error;
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $helper;
    /**
     * Model under test
     *
     * @var \Magento\Ups\Model\Carrier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $errorFactory;

    /**
     * @var \Magento\Ups\Model\Carrier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $carrier;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scope;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $country;

    /**
     * @var \Magento\Framework\Model\AbstractModel
     */
    protected $abstractModel;

    /**
     * @var \Magento\Shipping\Model\Rate\Result
     */
    protected $rate;

    protected function setUp()
    {
        $this->helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->scope = $this->getMockBuilder(
            '\Magento\Framework\App\Config\ScopeConfigInterface'
        )->disableOriginalConstructor()->getMock();

        $this->scope->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnCallback([$this, 'scopeConfiggetValue'])
        );

        $this->error = $this->getMockBuilder('\Magento\Quote\Model\Quote\Address\RateResult\Error')
            ->setMethods(['setCarrier', 'setCarrierTitle', 'setErrorMessage'])
            ->getMock();

        $this->errorFactory = $this->getMockBuilder('Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->errorFactory->expects($this->any())->method('create')->willReturn($this->error);

        $this->rate = $this->getMock('Magento\Shipping\Model\Rate\Result', ['getError'], [], '', false);
        $rateFactory = $this->getMock('Magento\Shipping\Model\Rate\ResultFactory', ['create'], [], '', false);

        $rateFactory->expects($this->any())->method('create')->willReturn($this->rate);

        $this->country = $this->getMockBuilder('\Magento\Directory\Model\Country')
            ->disableOriginalConstructor()
            ->setMethods(['load'])
            ->getMock();

        $this->abstractModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->disableOriginalConstructor()
            ->setMethods(['getData'])
            ->getMock();

        $this->country->expects($this->any())->method('load')->willReturn($this->abstractModel);

        $this->countryFactory = $this->getMockBuilder('\Magento\Directory\Model\CountryFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->countryFactory->expects($this->any())->method('create')->willReturn($this->country);

        $this->model = $this->helper->getObject(
            'Magento\Ups\Model\Carrier',
            [
                'scopeConfig' => $this->scope,
                'rateErrorFactory' => $this->errorFactory,
                'countryFactory' => $this->countryFactory,
                'rateFactory' => $rateFactory
            ]
        );
    }

    /**
     * Callback function, emulates getValue function
     * @param $path
     * @return null|string
     */
    public function scopeConfiggetValue($path)
    {
        $pathMap = [
            'carriers/ups/free_method' => 'free_method',
            'carriers/ups/free_shipping_subtotal' => 5,
            'carriers/ups/showmethod' => 1,
            'carriers/ups/title' => 'ups Title',
            'carriers/ups/specificerrmsg' => 'ups error message',
            'carriers/ups/min_package_weight' => 2,
            'carriers/ups/type' => 'UPS',
        ];

        return isset($pathMap[$path]) ? $pathMap[$path] : null;
    }

    /**
     * @dataProvider getMethodPriceProvider
     * @param int $cost
     * @param string $shippingMethod
     * @param bool $freeShippingEnabled
     * @param int $requestSubtotal
     * @param int $expectedPrice
     * @covers       \Magento\Shipping\Model\Carrier\AbstractCarrierOnline::getMethodPrice
     */
    public function testGetMethodPrice(
        $cost,
        $shippingMethod,
        $freeShippingEnabled,
        $requestSubtotal,
        $expectedPrice
    ) {
        $path = 'carriers/' . $this->model->getCarrierCode() . '/';
        $this->scope->expects($this->any())->method('isSetFlag')->with($path . 'free_shipping_enable')->will(
            $this->returnValue($freeShippingEnabled)
        );

        $request = new \Magento\Quote\Model\Quote\Address\RateRequest();
        $request->setBaseSubtotalInclTax($requestSubtotal);
        $this->model->setRawRequest($request);
        $price = $this->model->getMethodPrice($cost, $shippingMethod);
        $this->assertEquals($expectedPrice, $price);
    }

    /**
     * Data provider for testGenerate method
     *
     * @return array
     */
    public function getMethodPriceProvider()
    {
        return [
            [3, self::FREE_METHOD_NAME, true, 6, 0],
            [3, self::FREE_METHOD_NAME, true, 4, 3],
            [3, self::FREE_METHOD_NAME, false, 6, 3],
            [3, self::FREE_METHOD_NAME, false, 4, 3],
            [3, self::PAID_METHOD_NAME, true, 6, 3],
            [3, self::PAID_METHOD_NAME, true, 4, 3],
            [3, self::PAID_METHOD_NAME, false, 6, 3],
            [3, self::PAID_METHOD_NAME, false, 4, 3],
            [7, self::FREE_METHOD_NAME, true, 6, 0],
            [7, self::FREE_METHOD_NAME, true, 4, 7],
            [7, self::FREE_METHOD_NAME, false, 6, 7],
            [7, self::FREE_METHOD_NAME, false, 4, 7],
            [7, self::PAID_METHOD_NAME, true, 6, 7],
            [7, self::PAID_METHOD_NAME, true, 4, 7],
            [7, self::PAID_METHOD_NAME, false, 6, 7],
            [7, self::PAID_METHOD_NAME, false, 4, 7],
            [3, self::FREE_METHOD_NAME, true, 0, 3],
            [3, self::FREE_METHOD_NAME, true, 0, 3],
            [3, self::FREE_METHOD_NAME, false, 0, 3],
            [3, self::FREE_METHOD_NAME, false, 0, 3],
            [3, self::PAID_METHOD_NAME, true, 0, 3],
            [3, self::PAID_METHOD_NAME, true, 0, 3],
            [3, self::PAID_METHOD_NAME, false, 0, 3],
            [3, self::PAID_METHOD_NAME, false, 0, 3]
        ];
    }

    public function testCollectRatesErrorMessage()
    {
        $this->scope->expects($this->once())->method('isSetFlag')->willReturn(false);

        $this->error->expects($this->once())->method('setCarrier')->with('ups');
        $this->error->expects($this->once())->method('setCarrierTitle');
        $this->error->expects($this->once())->method('setErrorMessage');

        $request = new RateRequest();
        $request->setPackageWeight(1);

        $this->assertSame($this->error, $this->model->collectRates($request));
    }

    public function testCollectRatesFail()
    {
        $this->scope->expects($this->once())->method('isSetFlag')->willReturn(true);

        $request = new RateRequest();
        $request->setPackageWeight(1);

        $this->assertSame($this->rate, $this->model->collectRates($request));
    }
}
