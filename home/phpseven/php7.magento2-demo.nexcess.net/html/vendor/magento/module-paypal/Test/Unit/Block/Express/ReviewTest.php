<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Test\Unit\Block\Express;

use Magento\Paypal\Block\Express\Review;
use Magento\Quote\Model\Quote\Address\Rate;

class ReviewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetRepo;

    /**
     * @var Review
     */
    protected $model;

    protected function setUp()
    {
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $layout = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);

        $scopeConfig->expects($this->any())
            ->method('getValue')
            ->with(
                $this->stringContains('advanced/modules_disable_output/'),
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )->will($this->returnValue(false));

        $urlBuilder = $this->getMock('Magento\Framework\UrlInterface');
        $urlBuilder->expects($this->any())->method('getUrl')->will($this->returnArgument(0));

        $context = $this->getMock(
            'Magento\Framework\View\Element\Template\Context',
            ['getLayout', 'getEventManager', 'getScopeConfig', 'getRequest', 'getAssetRepository', 'getUrlBuilder'],
            [],
            '',
            false
        );

        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->assetRepo = $this->getMock('Magento\Framework\View\Asset\Repository', [], [], '', false);

        $context->expects($this->any())->method('getLayout')->will($this->returnValue($layout));
        $context->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));
        $context->expects($this->any())->method('getScopeConfig')->will($this->returnValue($scopeConfig));
        $context->expects($this->any())->method('getRequest')->will($this->returnValue($this->request));
        $context->expects($this->any())->method('getAssetRepository')->will($this->returnValue($this->assetRepo));
        $context->expects($this->any())->method('getUrlBuilder')->will($this->returnValue($urlBuilder));

        $this->model = $helper->getObject('Magento\Paypal\Block\Express\Review', ['context' => $context]);
    }

    /**
     * @param bool $isSecure
     * @dataProvider getViewFileUrlDataProvider
     */
    public function testGetViewFileUrl($isSecure)
    {
        $this->request->expects($this->once())->method('isSecure')->will($this->returnValue($isSecure));
        $this->assetRepo->expects($this->once())
            ->method('getUrlWithParams')
            ->with('some file', $this->callback(function ($value) use ($isSecure) {
                return isset($value['_secure']) && $value['_secure'] === $isSecure;
            }))
            ->will($this->returnValue('result url'));
        $this->assertEquals('result url', $this->model->getViewFileUrl('some file'));
    }

    public function getViewFileUrlDataProvider()
    {
        return [[true], [false]];
    }

    public function testBeforeToHtmlWhenQuoteIsNotVirtual()
    {
        $quote = $this->_getQuoteMock();
        $quote->expects($this->any())->method('getIsVirtual')->will($this->returnValue(false));
        $quote->setMayEditShippingMethod('MayEditShippingMethod');

        $shippingRate = new \Magento\Framework\DataObject(['code' => 'Rate 1']);
        $shippingRates = [
            [$shippingRate],
        ];
        $quote->getShippingAddress()
            ->expects($this->any())
            ->method('getGroupedAllShippingRates')
            ->will($this->returnValue($shippingRates));
        $quote->getShippingAddress()
            ->expects($this->any())
            ->method('getShippingMethod')
            ->will($this->returnValue($shippingRate->getCode()));

        $this->model->setQuote($quote);
        $this->model->toHtml();

        $this->assertEquals(
            $this->model->getPaymentMethodTitle(),
            $quote->getPayment()->getMethodInstance()->getTitle()
        );
        $this->assertTrue($this->model->getShippingRateRequired());
        $this->assertSame($shippingRates, $this->model->getShippingRateGroups());
        $this->assertSame($shippingRate, $this->model->getCurrentShippingRate());
        $this->assertNotNull($this->model->getCanEditShippingAddress());
        $this->assertEquals($quote->getMayEditShippingMethod(), $this->model->getCanEditShippingMethod());
        $this->assertContains('paypal/express/saveShippingMethod', $this->model->getShippingMethodSubmitUrl());
        $this->assertContains('paypal/express/edit', $this->model->getEditUrl());
        $this->assertContains('paypal/express/placeOrder', $this->model->getPlaceOrderUrl());
    }

    public function testBeforeToHtmlWhenQuoteIsVirtual()
    {
        $quote = $this->_getQuoteMock();
        $quote->expects($this->any())->method('getIsVirtual')->will($this->returnValue(true));
        $this->model->setQuote($quote);
        $this->model->toHtml();
        $this->assertEquals(
            $this->model->getPaymentMethodTitle(),
            $quote->getPayment()->getMethodInstance()->getTitle()
        );
        $this->assertFalse($this->model->getShippingRateRequired());
        $this->assertContains('paypal/express/edit', $this->model->getEditUrl());
        $this->assertContains('paypal/express/placeOrder', $this->model->getPlaceOrderUrl());
    }

    /**
     * Create mock of sales quote model
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getQuoteMock()
    {
        $methodInstance = new \Magento\Framework\DataObject(['title' => 'Payment Method']);
        $payment = $this->getMock('Magento\Quote\Model\Quote\Payment', [], [], '', false);
        $payment->expects($this->any())->method('getMethodInstance')->will($this->returnValue($methodInstance));

        $quote = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);
        $quote->expects($this->any())->method('getPayment')->will($this->returnValue($payment));
        $quote->setPayment($payment);

        $address = $this->getMockBuilder('Magento\Quote\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->setMethods(['getShippingMethod', 'getGroupedAllShippingRates', '__wakeup'])
            ->getMock();
        $quote->expects($this->any())->method('getShippingAddress')->will($this->returnValue($address));

        return $quote;
    }

    public function testGetEmail()
    {
        $quoteMock = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);
        $billingAddressMock = $this->getMock('\Magento\Quote\Model\Quote\Address', [], [], '', false);
        $quoteMock->expects($this->once())->method('getBillingAddress')->willReturn($billingAddressMock);
        $billingAddressMock->expects($this->once())->method('getEmail')->willReturn('test@example.com');
        $this->model->setQuote($quoteMock);
        $this->assertEquals('test@example.com', $this->model->getEmail());
    }

    public function testGetEmailWhenBillingAddressNotExist()
    {
        $quoteMock = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);
        $quoteMock->expects($this->once())->method('getBillingAddress')->willReturn(null);
        $this->model->setQuote($quoteMock);
        $this->assertEquals('', $this->model->getEmail());
    }

    public function testCanEditShippingMethod()
    {
        $this->model->setData('can_edit_shipping_method', true);
        static::assertTrue($this->model->canEditShippingMethod());

        $this->model->setData('can_edit_shipping_method', false);
        static::assertTrue($this->model->canEditShippingMethod());
    }
}
