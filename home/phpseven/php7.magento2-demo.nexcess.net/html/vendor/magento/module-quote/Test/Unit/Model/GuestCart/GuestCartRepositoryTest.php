<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Quote\Test\Unit\Model\GuestCart;

class GuestCartRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Quote\Model\GuestCart\GuestCartRepository
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteIdMaskFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteIdMaskMock;

    /**
     * @var string
     */
    protected $maskedCartId;

    /**
     * @var int
     */
    protected $cartId;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->quoteRepositoryMock = $this->getMock('Magento\Quote\Api\CartRepositoryInterface', [], [], '', false);
        $this->quoteMock = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);

        $this->maskedCartId = 'f216207248d65c789b17be8545e0aa73';
        $this->cartId = 123;

        $guestCartTestHelper = new GuestCartTestHelper($this);
        list($this->quoteIdMaskFactoryMock, $this->quoteIdMaskMock) = $guestCartTestHelper->mockQuoteIdMask(
            $this->maskedCartId,
            $this->cartId
        );

        $this->model = $objectManager->getObject(
            'Magento\Quote\Model\GuestCart\GuestCartRepository',
            [
                'quoteRepository' => $this->quoteRepositoryMock,
                'quoteIdMaskFactory' => $this->quoteIdMaskFactoryMock
            ]
        );
    }

    public function testGet()
    {
        $this->quoteRepositoryMock->expects($this->once())->method('get')->willReturn($this->quoteMock);
        $this->assertEquals($this->quoteMock, $this->model->get($this->maskedCartId));
    }
}
