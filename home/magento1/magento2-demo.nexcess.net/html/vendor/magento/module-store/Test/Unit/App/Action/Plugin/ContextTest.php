<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Store\Test\Unit\App\Action\Plugin;

use Magento\Framework\App\Http\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ContextPluginTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    const CURRENCY_SESSION = 'CNY';
    const CURRENCY_DEFAULT = 'USD';
    const CURRENCY_CURRENT_STORE = 'UAH';

    /**
     * @var \Magento\Store\App\Action\Plugin\Context
     */
    protected $plugin;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\Framework\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContextMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpRequestMock;

    /**
     * @var \Magento\Store\Model\StoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Api\StoreCookieManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeCookieManager;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currentStoreMock;

    /**
     * @var \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $websiteMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->sessionMock = $this->getMock(
            'Magento\Framework\Session\Generic',
            ['getCurrencyCode'],
            [],
            '',
            false
        );
        $this->httpContextMock = $this->getMock(
            'Magento\Framework\App\Http\Context',
            [],
            [],
            '',
            false
        );
        $this->httpRequestMock = $this->getMock(
            'Magento\Framework\App\Request\Http',
            ['getParam'],
            [],
            '',
            false
        );
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->storeCookieManager = $this->getMock('Magento\Store\Api\StoreCookieManagerInterface');
        $this->storeMock = $this->getMock(
            'Magento\Store\Model\Store',
            [],
            [],
            '',
            false
        );
        $this->currentStoreMock = $this->getMock(
            'Magento\Store\Model\Store',
            [],
            [],
            '',
            false
        );
        $this->websiteMock = $this->getMock(
            'Magento\Store\Model\Website',
            ['getDefaultStore', '__wakeup'],
            [],
            '',
            false
        );
        $this->closureMock = function () {
            return 'ExpectedValue';
        };
        $this->subjectMock = $this->getMock(
            'Magento\Framework\App\Action\Action',
            [],
            [],
            '',
            false
        );
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');

        $this->plugin = (new ObjectManager($this))->getObject(
            'Magento\Store\App\Action\Plugin\Context',
            [
                'session' => $this->sessionMock,
                'httpContext' => $this->httpContextMock,
                'httpRequest' => $this->httpRequestMock,
                'storeManager' => $this->storeManager,
                'storeCookieManager' => $this->storeCookieManager,
            ]
        );
        $this->storeManager->expects($this->once())
            ->method('getWebsite')
            ->will($this->returnValue($this->websiteMock));
        $this->storeManager->method('getDefaultStoreView')
            ->willReturn($this->storeMock);
        $this->storeManager->method('getStore')
            ->willReturn($this->currentStoreMock);
        $this->websiteMock->expects($this->once())
            ->method('getDefaultStore')
            ->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())
            ->method('getDefaultCurrencyCode')
            ->will($this->returnValue(self::CURRENCY_DEFAULT));
        $this->storeMock->expects($this->once())
            ->method('getCode')
            ->willReturn('default');
        $this->currentStoreMock->expects($this->once())
            ->method('getCode')
            ->willReturn('custom_store');
        $this->storeCookieManager->expects($this->once())
            ->method('getStoreCodeFromCookie')
            ->will($this->returnValue('storeCookie'));
        $this->httpRequestMock->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('___store'))
            ->will($this->returnValue('default'));
        $this->currentStoreMock->expects($this->any())
            ->method('getDefaultCurrencyCode')
            ->will($this->returnValue(self::CURRENCY_CURRENT_STORE));
    }

    public function testAroundDispatchCurrencyFromSession()
    {
        $this->sessionMock->expects($this->any())
            ->method('getCurrencyCode')
            ->will($this->returnValue(self::CURRENCY_SESSION));

        $this->httpContextMock->expects($this->at(0))
            ->method('setValue')
            ->with(StoreManagerInterface::CONTEXT_STORE, 'custom_store', 'default');
        /** Make sure that current currency is taken from session if available */
        $this->httpContextMock->expects($this->at(1))
            ->method('setValue')
            ->with(Context::CONTEXT_CURRENCY, self::CURRENCY_SESSION, self::CURRENCY_DEFAULT);

        $this->assertEquals(
            'ExpectedValue',
            $this->plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function testDispatchCurrentStoreCurrency()
    {
        $this->httpContextMock->expects($this->at(0))
            ->method('setValue')
            ->with(StoreManagerInterface::CONTEXT_STORE, 'custom_store', 'default');
        /** Make sure that current currency is taken from current store if no value is provided in session */
        $this->httpContextMock->expects($this->at(1))
            ->method('setValue')
            ->with(Context::CONTEXT_CURRENCY, self::CURRENCY_CURRENT_STORE, self::CURRENCY_DEFAULT);

        $this->assertEquals(
            'ExpectedValue',
            $this->plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }
}
