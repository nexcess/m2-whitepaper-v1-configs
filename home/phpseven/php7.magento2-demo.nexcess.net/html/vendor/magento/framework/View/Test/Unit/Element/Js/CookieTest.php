<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\View\Test\Unit\Element\Js;

class CookieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Element\Js\Cookie
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ipValidatorMock;

    public function setUp()
    {
        $this->contextMock = $this->getMockBuilder('Magento\Framework\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionConfigMock = $this->getMockBuilder('Magento\Framework\Session\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->ipValidatorMock = $this->getMockBuilder('Magento\Framework\Validator\Ip')
            ->disableOriginalConstructor()
            ->getMock();

        $validtorMock = $this->getMockBuilder('Magento\Framework\View\Element\Template\File\Validator')
            ->setMethods(['isValid'])->disableOriginalConstructor()->getMock();

        $scopeConfigMock = $this->getMockBuilder('Magento\Framework\App\Config')
            ->setMethods(['isSetFlag'])->disableOriginalConstructor()->getMock();

        $this->contextMock->expects($this->any())
            ->method('getScopeConfig')
            ->will($this->returnValue($scopeConfigMock));

        $this->contextMock->expects($this->any())
            ->method('getValidator')
            ->will($this->returnValue($validtorMock));

        $this->model = new \Magento\Framework\View\Element\Js\Cookie(
            $this->contextMock,
            $this->sessionConfigMock,
            $this->ipValidatorMock,
            []
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('Magento\Framework\View\Element\Js\Cookie', $this->model);
    }

    /**
     * @dataProvider domainDataProvider
     */
    public function testGetDomain($domain, $isIp, $expectedResult)
    {
        $this->sessionConfigMock->expects($this->once())
            ->method('getCookieDomain')
            ->will($this->returnValue($domain));
        $this->ipValidatorMock->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo($domain))
            ->will($this->returnValue($isIp));

        $result = $this->model->getDomain($domain);
        $this->assertEquals($expectedResult, $result);
    }

    public static function domainDataProvider()
    {
        return [
            ['127.0.0.1', true, '127.0.0.1'],
            ['example.com', false, '.example.com'],
            ['.example.com', false, '.example.com'],
        ];
    }

    public function testGetPath()
    {
        $path = 'test_path';

        $this->sessionConfigMock->expects($this->once())
            ->method('getCookiePath')
            ->will($this->returnValue($path));

        $result = $this->model->getPath();
        $this->assertEquals($path, $result);
    }

    public function testGetLifetime()
    {
        $lifetime = 3600;
        $this->sessionConfigMock->expects(static::once())
            ->method('getCookieLifetime')
            ->willReturn($lifetime);

        $this->assertEquals($lifetime, $this->model->getLifetime());
    }
}
