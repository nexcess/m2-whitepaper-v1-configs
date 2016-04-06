<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Unit\Pricing\Render;

/**
 * Class FinalPriceBoxTest
 */
class FinalPriceBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Render\FinalPriceBox
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfo;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceBox;

    /**
     * @var \Magento\Framework\View\LayoutInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Pricing\Render\RendererPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rendererPool;

    /**
     * @var \Magento\Framework\Pricing\Price\PriceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $price;

    protected function setUp()
    {
        $this->product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getPriceInfo', '__wakeup', 'getCanShowPrice'],
            [],
            '',
            false
        );
        $this->priceInfo = $this->getMock('Magento\Framework\Pricing\PriceInfo', ['getPrice'], [], '', false);
        $this->product->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfo));

        $eventManager = $this->getMock('Magento\Framework\Event\Test\Unit\ManagerStub', [], [], '', false);
        $config = $this->getMock('Magento\Store\Model\Store\Config', [], [], '', false);
        $this->layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);

        $this->priceBox = $this->getMock('Magento\Framework\Pricing\Render\PriceBox', [], [], '', false);
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');

        $this->layout->expects($this->any())
            ->method('getBlock')
            ->will($this->returnValue($this->priceBox));

        $scopeConfigMock = $this->getMockForAbstractClass('Magento\Framework\App\Config\ScopeConfigInterface');
        $context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $context->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));
        $context->expects($this->any())
            ->method('getStoreConfig')
            ->will($this->returnValue($config));
        $context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));
        $context->expects($this->any())
            ->method('getLogger')
            ->will($this->returnValue($this->logger));
        $context->expects($this->any())
            ->method('getScopeConfig')
            ->will($this->returnValue($scopeConfigMock));

        $this->rendererPool = $this->getMockBuilder('Magento\Framework\Pricing\Render\RendererPool')
            ->disableOriginalConstructor()
            ->getMock();

        $this->price = $this->getMock('Magento\Framework\Pricing\Price\PriceInterface');
        $this->price->expects($this->any())
            ->method('getPriceCode')
            ->will($this->returnValue(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE));

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->object = $objectManager->getObject(
            'Magento\Catalog\Pricing\Render\FinalPriceBox',
            [
                'context' => $context,
                'saleableItem' => $this->product,
                'rendererPool' => $this->rendererPool,
                'price' => $this->price,
                'data' => ['zone' => 'test_zone']
            ]
        );
    }

    public function testRenderMsrpDisabled()
    {
        $priceType = $this->getMock('Magento\Msrp\Pricing\Price\MsrpPrice', [], [], '', false);
        $this->priceInfo->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo('msrp_price'))
            ->will($this->returnValue($priceType));

        $priceType->expects($this->any())
            ->method('canApplyMsrp')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(false));

        $result = $this->object->toHtml();

        //assert price wrapper
        $this->assertStringStartsWith('<div', $result);
        //assert css_selector
        $this->assertRegExp('/[final_price]/', $result);
    }

    public function testRenderMsrpEnabled()
    {
        $priceType = $this->getMock('Magento\Msrp\Pricing\Price\MsrpPrice', [], [], '', false);
        $this->priceInfo->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo('msrp_price'))
            ->will($this->returnValue($priceType));

        $priceType->expects($this->any())
            ->method('canApplyMsrp')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(true));

        $priceType->expects($this->any())
            ->method('isMinimalPriceLessMsrp')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(true));

        $priceBoxRender = $this->getMockBuilder('Magento\Framework\Pricing\Render\PriceBox')
            ->disableOriginalConstructor()
            ->getMock();
        $priceBoxRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('test'));

        $arguments = [
            'real_price_html' => '',
            'zone' => 'test_zone',
        ];
        $this->rendererPool->expects($this->once())
            ->method('createPriceRender')
            ->with('msrp_price', $this->product, $arguments)
            ->will($this->returnValue($priceBoxRender));

        $result = $this->object->toHtml();

        //assert price wrapper
        $this->assertEquals(
            '<div class="price-box price-final_price" data-role="priceBox" data-product-id="">test</div>',
            $result
        );
    }

    public function testRenderMsrpNotRegisteredException()
    {
        $this->logger->expects($this->once())
            ->method('critical');

        $this->priceInfo->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo('msrp_price'))
            ->will($this->throwException(new \InvalidArgumentException()));

        $result = $this->object->toHtml();

        //assert price wrapper
        $this->assertStringStartsWith('<div', $result);
        //assert css_selector
        $this->assertRegExp('/[final_price]/', $result);
    }

    public function testRenderAmountMinimal()
    {
        $priceType = $this->getMock('Magento\Catalog\Pricing\Price\FinalPrice', [], [], '', false);
        $amount = $this->getMockForAbstractClass('Magento\Framework\Pricing\Amount\AmountInterface');
        $priceId = 'price_id';
        $html = 'html';
        $this->object->setData('price_id', $priceId);

        $arguments = [
            'zone' => 'test_zone',
            'display_label' => 'As low as',
            'price_id' => $priceId,
            'include_container' => false,
            'skip_adjustments' => true,
        ];

        $amountRender = $this->getMock('Magento\Framework\Pricing\Render\Amount', ['toHtml'], [], '', false);
        $amountRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($html));

        $this->priceInfo->expects($this->once())
            ->method('getPrice')
            ->with(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)
            ->will($this->returnValue($priceType));

        $priceType->expects($this->once())
            ->method('getMinimalPrice')
            ->will($this->returnValue($amount));

        $this->rendererPool->expects($this->once())
            ->method('createAmountRender')
            ->with($amount, $this->product, $this->price, $arguments)
            ->will($this->returnValue($amountRender));

        $this->assertEquals($html, $this->object->renderAmountMinimal());
    }

    /**
     * @dataProvider hasSpecialPriceProvider
     * @param float $regularPrice
     * @param float $finalPrice
     * @param bool $expectedResult
     */
    public function testHasSpecialPrice($regularPrice, $finalPrice, $expectedResult)
    {
        $regularPriceType = $this->getMock('Magento\Catalog\Pricing\Price\RegularPrice', [], [], '', false);
        $finalPriceType = $this->getMock('Magento\Catalog\Pricing\Price\FinalPrice', [], [], '', false);
        $regularPriceAmount = $this->getMockForAbstractClass('Magento\Framework\Pricing\Amount\AmountInterface');
        $finalPriceAmount = $this->getMockForAbstractClass('Magento\Framework\Pricing\Amount\AmountInterface');

        $regularPriceAmount->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($regularPrice));
        $finalPriceAmount->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($finalPrice));

        $regularPriceType->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue($regularPriceAmount));
        $finalPriceType->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue($finalPriceAmount));

        $this->priceInfo->expects($this->at(0))
            ->method('getPrice')
            ->with(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)
            ->will($this->returnValue($regularPriceType));
        $this->priceInfo->expects($this->at(1))
            ->method('getPrice')
            ->with(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)
            ->will($this->returnValue($finalPriceType));

        $this->assertEquals($expectedResult, $this->object->hasSpecialPrice());
    }

    public function hasSpecialPriceProvider()
    {
        return [
            [10.0, 20.0, false],
            [20.0, 10.0, true],
            [10.0, 10.0, false]
        ];
    }

    public function testShowMinimalPrice()
    {
        $finalPrice = 10.0;
        $minimalPrice = 5.0;
        $displayMininmalPrice = 2.0;

        $this->object->setDisplayMinimalPrice($displayMininmalPrice);

        $finalPriceType = $this->getMock('Magento\Catalog\Pricing\Price\FinalPrice', [], [], '', false);

        $finalPriceAmount = $this->getMockForAbstractClass('Magento\Framework\Pricing\Amount\AmountInterface');
        $minimalPriceAmount = $this->getMockForAbstractClass('Magento\Framework\Pricing\Amount\AmountInterface');

        $finalPriceAmount->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($finalPrice));
        $minimalPriceAmount->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($minimalPrice));

        $finalPriceType->expects($this->at(0))
            ->method('getAmount')
            ->will($this->returnValue($finalPriceAmount));
        $finalPriceType->expects($this->at(1))
            ->method('getMinimalPrice')
            ->will($this->returnValue($minimalPriceAmount));

        $this->priceInfo->expects($this->once())
            ->method('getPrice')
            ->with(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)
            ->will($this->returnValue($finalPriceType));

        $this->assertTrue($this->object->showMinimalPrice());
    }

    public function testHidePrice()
    {
        $this->product->expects($this->any())
            ->method('getCanShowPrice')
            ->will($this->returnValue(false));

        $this->assertEmpty($this->object->toHtml());
    }
}
