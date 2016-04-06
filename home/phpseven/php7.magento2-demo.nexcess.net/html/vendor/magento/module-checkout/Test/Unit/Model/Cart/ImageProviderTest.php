<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Checkout\Test\Unit\Model\Cart;

class ImageProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Checkout\Model\Cart\ImageProvider
     */
    public $model;

    /**
     * @var \PHPUnit_Framework_Mockobject_Mockobject | \Magento\Quote\Api\CartItemRepositoryInterface
     */
    protected $itemRepositoryMock;

    /**
     * @var \PHPUnit_Framework_Mockobject_Mockobject | \Magento\Checkout\CustomerData\ItemPoolInterface
     */
    protected $itemPoolMock;

    protected function setUp()
    {
        $this->itemRepositoryMock = $this->getMock('Magento\Quote\Api\CartItemRepositoryInterface', [], [], '', false);
        $this->itemPoolMock = $this->getMock('Magento\Checkout\CustomerData\ItemPoolInterface', [], [], '', false);
        $this->model = new \Magento\Checkout\Model\Cart\ImageProvider(
            $this->itemRepositoryMock,
            $this->itemPoolMock
        );
    }

    public function testGetImages()
    {
        $cartId = 42;
        $itemId = 74;
        $itemData = ['product_image' => 'Magento.png', 'random' => '3.1415926535'];
        $itemMock = $this->getMock('Magento\Quote\Model\Quote\Item', [], [], '', false);
        $itemMock->expects($this->once())->method('getItemId')->willReturn($itemId);

        $expectedResult = [$itemId => $itemData['product_image']];

        $this->itemRepositoryMock->expects($this->once())->method('getList')->with($cartId)->willReturn([$itemMock]);
        $this->itemPoolMock->expects($this->once())->method('getItemData')->with($itemMock)->willReturn($itemData);

        $this->assertEquals($expectedResult, $this->model->getImages($cartId));
    }
}
