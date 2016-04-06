<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Sales\Test\Unit\Block\Adminhtml\Order\Create\Items;

class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\Block\Template
     */
    protected $priceRenderBlock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\Layout
     */
    protected $layoutMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Quote\Model\Quote\Item  */
    protected $itemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $stockItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockState;

    /**
     * Initialize required data
     */
    protected function setUp()
    {
        $orderCreateMock = $this->getMock('Magento\Sales\Model\AdminOrder\Create', ['__wakeup'], [], '', false);
        $taxData = $this->getMockBuilder('Magento\Tax\Helper\Data')->disableOriginalConstructor()->getMock();
        $this->priceCurrency = $this->getMockBuilder('\Magento\Framework\Pricing\PriceCurrencyInterface')->getMock();
        $sessionMock = $this->getMockBuilder('Magento\Backend\Model\Session\Quote')
            ->disableOriginalConstructor()
            ->setMethods(['getQuote', '__wakeup'])
            ->getMock();

        $quoteMock = $this->getMockBuilder('Magento\Quote\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods(['getStore', '__wakeup'])
            ->getMock();

        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();
        $this->priceCurrency->expects($this->any())
            ->method('convertAndFormat')
            ->will($this->returnArgument(0));
        $quoteMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));
        $sessionMock->expects($this->any())->method('getQuote')->will($this->returnValue($quoteMock));
        $wishlistFactoryMock = $this->getMockBuilder('Magento\Wishlist\Model\WishlistFactory')
            ->disableOriginalConstructor()
            ->setMethods(['methods', '__wakeup'])
            ->getMock();

        $giftMessageSave = $this->getMockBuilder('Magento\Giftmessage\Model\Save')
            ->setMethods(['__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $taxConfig = $this->getMockBuilder('Magento\Tax\Model\Config')->disableOriginalConstructor()->getMock();

        $this->stockRegistry = $this->getMockBuilder('Magento\CatalogInventory\Model\StockRegistry')
            ->disableOriginalConstructor()
            ->setMethods(['getStockItem', '__wakeup'])
            ->getMock();

        $this->stockItemMock = $this->getMock(
            'Magento\CatalogInventory\Model\Stock\Item',
            ['getIsInStock', '__wakeup'],
            [],
            '',
            false
        );

        $this->stockState = $this->getMock(
            'Magento\CatalogInventory\Model\StockState',
            ['checkQuoteItemQty', '__wakeup'],
            [],
            '',
            false
        );

        $this->stockRegistry->expects($this->any())
            ->method('getStockItem')
            ->will($this->returnValue($this->stockItemMock));

        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->block = $this->objectManager->getObject(
            'Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid',
            [
                'wishlistFactory' => $wishlistFactoryMock,
                'giftMessageSave' => $giftMessageSave,
                'taxConfig' => $taxConfig,
                'taxData' => $taxData,
                'sessionQuote' => $sessionMock,
                'orderCreate' => $orderCreateMock,
                'priceCurrency' => $this->priceCurrency,
                'stockRegistry' => $this->stockRegistry,
                'stockState' => $this->stockState
            ]
        );

        $this->priceRenderBlock = $this->getMockBuilder('\Magento\Backend\Block\Template')
            ->disableOriginalConstructor()
            ->setMethods(['setItem', 'toHtml'])
            ->getMock();

        $this->layoutMock = $this->getMockBuilder('\Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->setMethods(['getBlock'])
            ->getMock();

        $this->itemMock = $this->getMockBuilder('\Magento\Quote\Model\Quote\Item')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();
    }

    /**
     * @param array $itemData
     * @param string $expectedMessage
     * @param string $productType
     * @dataProvider tierPriceDataProvider
     */
    public function testTierPriceInfo($itemData, $expectedMessage, $productType)
    {
        $itemMock = $this->prepareItem($itemData, $productType);
        $result = $this->block->getTierHtml($itemMock);
        $this->assertEquals($expectedMessage, $result);
    }

    /**
     * Provider for test
     *
     * @return array
     */
    public function tierPriceDataProvider()
    {
        return [
            [
                [['price' => 100, 'price_qty' => 1]],
                '1 with 100% discount each',
                \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
            ],
            [
                [['price' => 100, 'price_qty' => 1], ['price' => 200, 'price_qty' => 2]],
                '1 with 100% discount each<br />2 with 200% discount each',
                \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
            ],
            [
                [['price' => 50, 'price_qty' => 2]],
                '2 for 50',
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            ],
            [
                [['price' => 50, 'price_qty' => 2], ['price' => 150, 'price_qty' => 3]],
                '2 for 50<br />3 for 150',
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            ],
            [0, '', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE]
        ];
    }

    /**
     * @param array|int $tierPrices
     * @param string $productType
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Quote\Model\Quote\Item
     */
    protected function prepareItem($tierPrices, $productType)
    {
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['getTierPrice', '__wakeup'])
            ->getMock();
        $product->expects($this->once())->method('getTierPrice')->will($this->returnValue($tierPrices));
        $item = $this->getMock(
            'Magento\Quote\Model\Quote\Item',
            [],
            ['getProduct', 'getProductType'],
            '',
            false
        );
        $item->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        $calledTimes = $tierPrices ? 'once' : 'never';
        $item->expects($this->{$calledTimes}())->method('getProductType')->will($this->returnValue($productType));
        return $item;
    }

    /**
     * @covers \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid::getItems
     */
    public function testGetItems()
    {
        $productId = 8;
        $itemQty = 23;
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface');
        $blockMock = $this->getMock('Magento\Framework\View\Element\AbstractBlock', ['getItems'], [], '', false);

        $itemMock = $this->getMock(
            'Magento\Quote\Model\Quote\Item',
            ['getProduct', 'setHasError', 'setQty', 'getQty', '__sleep', '__wakeup'],
            [],
            '',
            false
        );
        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getStockItem', 'getID', '__sleep', '__wakeup'],
            [],
            '',
            false
        );

        $checkMock = $this->getMock('Magento\Framework\DataObject', ['getMessage', 'getHasError'], [], '', false);

        $layoutMock->expects($this->once())->method('getParentName')->will($this->returnValue('parentBlock'));
        $layoutMock->expects($this->once())->method('getBlock')->with('parentBlock')
            ->will($this->returnValue($blockMock));

        $blockMock->expects($this->once())->method('getItems')->will($this->returnValue([$itemMock]));

        $itemMock->expects($this->any())->method('getChildren')->will($this->returnValue([$itemMock]));
        $itemMock->expects($this->any())->method('getProduct')->will($this->returnValue($productMock));
        $itemMock->expects($this->any())->method('getQty')->will($this->returnValue($itemQty));

        $productMock->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $productMock->expects($this->any())->method('getStatus')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED));

        $checkMock->expects($this->any())->method('getMessage')->will($this->returnValue('Message'));
        $checkMock->expects($this->any())->method('getHasError')->will($this->returnValue(false));

        $this->stockState->expects($this->once())
            ->method('checkQuoteItemQty')
            ->with(
                $productId,
                $itemQty,
                $itemQty,
                $itemQty,
                null
            )
            ->will($this->returnValue($checkMock));

        $this->block->getQuote()->setIsSuperMode(true);
        $items = $this->block->setLayout($layoutMock)->getItems();

        $this->assertEquals('Message', $items[0]->getMessage());
        $this->assertEquals(true, $this->block->getQuote()->getIsSuperMode());
    }

    protected function getGrid()
    {
        /** @var \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid  $grid */
        $grid = $this->objectManager->getObject(
            'Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid',
            [
                'context' => $this->objectManager->getObject(
                        'Magento\Backend\Block\Template\Context',
                        ['layout' => $this->layoutMock]
                    )
            ]
        );

        return $grid;
    }

    public function testGetItemUnitPriceHtml()
    {
        $html = '$34.28';

        $grid = $this->getGrid();

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('item_unit_price')
            ->will($this->returnValue($this->priceRenderBlock));

        $this->priceRenderBlock->expects($this->once())
            ->method('setItem')
            ->with($this->itemMock);

        $this->priceRenderBlock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($html));

        $this->assertEquals($html, $grid->getItemUnitPriceHtml($this->itemMock));
    }

    public function testGetItemRowTotalHtml()
    {
        $html = '$34.28';

        $grid = $this->getGrid();

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('item_row_total')
            ->will($this->returnValue($this->priceRenderBlock));

        $this->priceRenderBlock->expects($this->once())
            ->method('setItem')
            ->with($this->itemMock);

        $this->priceRenderBlock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($html));

        $this->assertEquals($html, $grid->getItemRowTotalHtml($this->itemMock));
    }

    public function testGetItemRowTotalWithDiscountHtml()
    {
        $html = '$34.28';

        $grid = $this->getGrid();

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('item_row_total_with_discount')
            ->will($this->returnValue($this->priceRenderBlock));

        $this->priceRenderBlock->expects($this->once())
            ->method('setItem')
            ->with($this->itemMock);

        $this->priceRenderBlock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($html));

        $this->assertEquals($html, $grid->getItemRowTotalWithDiscountHtml($this->itemMock));
    }

    /**
     * @param array $orderData
     * @param bool $displayTotalsIncludeTax
     * @param float $expected
     * @dataProvider getSubtotalWithDiscountDataProvider
     */
    public function testGetSubtotalWithDiscount($orderData, $displayTotalsIncludeTax, $expected)
    {
        $quoteAddressMock = $this->getMock(
            '\Magento\Quote\Model\Quote\Address',
            ['getSubtotal', 'getTaxAmount','getDiscountTaxCompensationAmount','getDiscountAmount'],
            [],
            '',
            false
        );
        $gridMock = $this->getMock(
            'Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid',
            ['getQuoteAddress','displayTotalsIncludeTax'],
            [],
            '',
            false
        );

        $gridMock->expects($this->any())->method('getQuoteAddress')
            ->will($this->returnValue($quoteAddressMock));
        $gridMock->expects($this->any())->method('displayTotalsIncludeTax')
            ->will($this->returnValue($displayTotalsIncludeTax));

        $quoteAddressMock->expects($this->once())
            ->method('getSubtotal')
            ->will($this->returnValue($orderData['subTotal']));

        $quoteAddressMock->expects($this->any())
            ->method('getTaxAmount')
            ->will($this->returnValue($orderData['taxAmount']));

        $quoteAddressMock->expects($this->any())
            ->method('getDiscountTaxCompensationAmount')
            ->will($this->returnValue($orderData['discountTaxCompensationAmount']));

        $quoteAddressMock->expects($this->once())
            ->method('getDiscountAmount')
            ->will($this->returnValue($orderData['discountAmount']));

        $this->assertEquals($expected, $gridMock->getSubtotalWithDiscount());
    }

    /**
     * @return array
     */
    public function getSubtotalWithDiscountDataProvider()
    {
        $result = [];
        $result['displayTotalsIncludeTaxTrue'] = [
            'orderData' => [
                'subTotal' => 32.59,
                'taxAmount' => 8.2,
                'discountTaxCompensationAmount' => 1.72,
                'discountAmount' => -10.24
            ],
            'displayTotalsIncludeTax'=> true,
            'expected' => 32.27
        ];
        $result['displayTotalsIncludeTaxFalse'] = [
            'orderData' => [
                'subTotal' => 66.67,
                'taxAmount' => 20,
                'discountTaxCompensationAmount' => 8,
                'discountAmount' => -34.67
            ],
            'displayTotalsIncludeTax'=> false,
            'expected' => 32
        ];
        return $result;
    }

}
