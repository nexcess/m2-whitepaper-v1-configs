<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\OfflinePayments\Test\Unit\Model;

use Magento\OfflinePayments\Model\CheckmoConfigProvider;
use Magento\OfflinePayments\Model\Checkmo;
use Magento\Framework\Escaper;

class CheckmoConfigProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var CheckmoConfigProvider */
    protected $model;

    /** @var Checkmo|\PHPUnit_Framework_MockObject_MockObject */
    protected $methodMock;

    /** @var Escaper|\PHPUnit_Framework_MockObject_MockObject */
    protected $escaperMock;

    protected function setUp()
    {
        $this->methodMock = $this->getMock('Magento\OfflinePayments\Model\Checkmo', [], [], '', false);

        $paymentHelperMock = $this->getMock('Magento\Payment\Helper\Data', [], [], '', false);
        $paymentHelperMock->expects($this->once())
            ->method('getMethodInstance')
            ->with(Checkmo::PAYMENT_METHOD_CHECKMO_CODE)
            ->willReturn($this->methodMock);

        $this->escaperMock = $this->getMock('Magento\Framework\Escaper');
        $this->escaperMock->expects($this->any())
            ->method('escapeHtml')
            ->willReturnArgument(0);

        $this->model = new CheckmoConfigProvider(
            $paymentHelperMock,
            $this->escaperMock
        );
    }

    /**
     * @param bool $isAvailable
     * @param string $mailingAddress
     * @param string $payableTo
     * @param array $result
     * @dataProvider dataProviderGetConfig
     */
    public function testGetConfig($isAvailable, $mailingAddress, $payableTo, $result)
    {
        $this->methodMock->expects($this->once())
            ->method('isAvailable')
            ->willReturn($isAvailable);
        $this->methodMock->expects($this->any())
            ->method('getMailingAddress')
            ->willReturn($mailingAddress);
        $this->methodMock->expects($this->any())
            ->method('getPayableTo')
            ->willReturn($payableTo);

        $this->assertEquals($result, $this->model->getConfig());
    }

    public function dataProviderGetConfig()
    {
        $checkmoCode = Checkmo::PAYMENT_METHOD_CHECKMO_CODE;
        return [
            [false, '', '', []],
            [true, '', '', ['payment' => [$checkmoCode => ['mailingAddress' => '', 'payableTo' => '']]]],
            [true, 'address', '', ['payment' => [$checkmoCode => ['mailingAddress' => 'address', 'payableTo' => '']]]],
            [true, '', 'to', ['payment' => [$checkmoCode => ['mailingAddress' => '', 'payableTo' => 'to']]]],
            [true, 'addr', 'to', ['payment' => [$checkmoCode => ['mailingAddress' => 'addr', 'payableTo' => 'to']]]],
            [false, 'addr', 'to', []],
        ];
    }
}
