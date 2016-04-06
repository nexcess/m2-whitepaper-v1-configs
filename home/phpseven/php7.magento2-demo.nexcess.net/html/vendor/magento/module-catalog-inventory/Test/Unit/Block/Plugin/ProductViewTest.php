<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogInventory\Test\Unit\Block\Plugin;

class ProductViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Block\Plugin\ProductView
     */
    protected $block;

    /**
     * @var \Magento\CatalogInventory\Api\Data\StockItemInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItem;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockRegistry;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->stockItem = $this->getMockBuilder('\Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->setMethods(['getQtyMinAllowed', 'getQtyMaxAllowed', 'getQtyIncrements'])
            ->getMock();

        $this->stockRegistry = $this->getMockBuilder('Magento\CatalogInventory\Api\StockRegistryInterface')
            ->getMock();

        $this->block = $objectManager->getObject(
            'Magento\CatalogInventory\Block\Plugin\ProductView',
            [
                'stockRegistry' => $this->stockRegistry
            ]
        );
    }

    public function testAfterGetQuantityValidators()
    {
        $result = [
            'validate-item-quantity' =>
                [
                    'minAllowed' => 2,
                    'maxAllowed' => 5,
                    'qtyIncrements' => 3
                ]
        ];
        $validators = [];
        $productViewBlock = $this->getMockBuilder('Magento\Catalog\Block\Product\View')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['_wakeup', 'getId', 'getStore'])
            ->getMock();
        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['getWebsiteId', '_wakeup'])
            ->getMock();

        $productViewBlock->expects($this->any())->method('getProduct')->willReturn($productMock);
        $productMock->expects($this->once())->method('getId')->willReturn('productId');
        $productMock->expects($this->once())->method('getStore')->willReturn($storeMock);
        $storeMock->expects($this->once())->method('getWebsiteId')->willReturn('websiteId');
        $this->stockRegistry->expects($this->once())
            ->method('getStockItem')
            ->with('productId', 'websiteId')
            ->willReturn($this->stockItem);
        $this->stockItem->expects($this->once())->method('getQtyMinAllowed')->willReturn(2);
        $this->stockItem->expects($this->any())->method('getQtyMaxAllowed')->willReturn(5);
        $this->stockItem->expects($this->any())->method('getQtyIncrements')->willReturn(3);

        $this->assertEquals($result, $this->block->afterGetQuantityValidators($productViewBlock, $validators));
    }
}
