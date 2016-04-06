<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogInventory\Test\Unit\Model\Spi;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class StockStateProviderTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockStateProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface
     */
    protected $stockStateProvider;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    /**
     * @var \Magento\Framework\Math\Division|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mathDivision;

    /**
     * @var \Magento\Framework\Locale\FormatInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeFormat;

    /**
     * @var \Magento\Framework\DataObject\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectFactory;

    /**
     * @var \Magento\Framework\DataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $object;

    /**
     * @var float
     */
    protected $qty = 50.5;

    /**
     * @var bool
     */
    protected $qtyCheckApplicable = true;

    /**
     * @var array
     */
    protected $stockItemMethods = [
        'getId',
        'getProductId',
        'getWebsiteId',
        'getStockId',
        'getQty',
        'getIsInStock',
        'getIsQtyDecimal',
        'getShowDefaultNotificationMessage',
        'getUseConfigMinQty',
        'getMinQty',
        'getUseConfigMinSaleQty',
        'getMinSaleQty',
        'getUseConfigMaxSaleQty',
        'getMaxSaleQty',
        'getUseConfigBackorders',
        'getBackorders',
        'getUseConfigNotifyStockQty',
        'getNotifyStockQty',
        'getUseConfigQtyIncrements',
        'getQtyIncrements',
        'getUseConfigEnableQtyInc',
        'getEnableQtyIncrements',
        'getUseConfigManageStock',
        'getManageStock',
        'getLowStockDate',
        'getIsDecimalDivided',
        'getStockStatusChangedAuto',
        'hasStockQty',
        'setStockQty',
        'getData',
        'getSuppressCheckQtyIncrements',
        'getIsChildItem',
        'getIsSaleable',
        'getOrderedItems',
        'setOrderedItems',
        'getProductName',
    ];

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->mathDivision = $this->getMock(
            '\Magento\Framework\Math\Division',
            ['getExactDivision'],
            [],
            '',
            false
        );

        $this->localeFormat = $this->getMockForAbstractClass(
            '\Magento\Framework\Locale\FormatInterface',
            ['getNumber']
        );
        $this->localeFormat->expects($this->any())
            ->method('getNumber')
            ->willReturn($this->qty);

        $this->object = $this->objectManagerHelper->getObject('Magento\Framework\DataObject');
        $this->objectFactory = $this->getMock('\Magento\Framework\DataObject\Factory', ['create'], [], '', false);
        $this->objectFactory->expects($this->any())->method('create')->willReturn($this->object);

        $this->product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['load', 'isComposite', '__wakeup', 'isSaleable'],
            [],
            '',
            false
        );
        $this->productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory', ['create'], [], '', false);
        $this->productFactory->expects($this->any())->method('create')->willReturn($this->product);

        $this->stockStateProvider = $this->objectManagerHelper->getObject(
            'Magento\CatalogInventory\Model\StockStateProvider',
            [
                'mathDivision' => $this->mathDivision,
                'localeFormat' => $this->localeFormat,
                'objectFactory' => $this->objectFactory,
                'productFactory' => $this->productFactory,
                'qtyCheckApplicable' => $this->qtyCheckApplicable
            ]
        );
    }

    protected function tearDown()
    {
        $this->stockStateProvider = null;
    }

    /**
     * @param StockItemInterface $stockItem
     * @param mixed $expectedResult
     * @dataProvider verifyStockDataProvider
     */
    public function testVerifyStock(StockItemInterface $stockItem, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->verifyStock($stockItem)
        );
    }

    /**
     * @param StockItemInterface $stockItem
     * @param mixed $expectedResult
     * @dataProvider verifyNotificationDataProvider
     */
    public function testVerifyNotification(StockItemInterface $stockItem, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->verifyNotification($stockItem)
        );
    }

    /**
     * @param StockItemInterface $stockItem
     * @param mixed $expectedResult
     * @dataProvider checkQtyDataProvider
     */
    public function testCheckQty(StockItemInterface $stockItem, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->checkQty($stockItem, $this->qty)
        );
    }

    /**
     * @param StockItemInterface $stockItem
     * @param mixed $expectedResult
     * @dataProvider suggestQtyDataProvider
     */
    public function testSuggestQty(StockItemInterface $stockItem, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->suggestQty($stockItem, $this->qty)
        );
    }

    /**
     * @param StockItemInterface $stockItem
     * @param mixed $expectedResult
     * @dataProvider getStockQtyDataProvider
     */
    public function testGetStockQty(StockItemInterface $stockItem, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->getStockQty($stockItem)
        );
    }

    /**
     * @param StockItemInterface $stockItem
     * @param mixed $expectedResult
     * @dataProvider checkQtyIncrementsDataProvider
     */
    public function testCheckQtyIncrements(StockItemInterface $stockItem, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->checkQtyIncrements($stockItem, $this->qty)->getHasError()
        );
    }

    /**
     * @param StockItemInterface $stockItem
     * @param mixed $expectedResult
     * @dataProvider checkQuoteItemQtyDataProvider
     */
    public function testCheckQuoteItemQty(StockItemInterface $stockItem, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->stockStateProvider->checkQuoteItemQty(
                $stockItem,
                $this->qty,
                $this->qty,
                $this->qty
            )->getHasError()
        );
    }

    public function verifyStockDataProvider()
    {
        return $this->prepareDataForMethod('verifyStock');
    }

    public function verifyNotificationDataProvider()
    {
        return $this->prepareDataForMethod('verifyNotification');
    }

    public function checkQtyDataProvider()
    {
        return $this->prepareDataForMethod('checkQty');
    }

    public function suggestQtyDataProvider()
    {
        return $this->prepareDataForMethod('suggestQty');
    }

    public function getStockQtyDataProvider()
    {
        return $this->prepareDataForMethod('getStockQty');
    }

    public function checkQtyIncrementsDataProvider()
    {
        return $this->prepareDataForMethod('checkQtyIncrements');
    }

    public function checkQuoteItemQtyDataProvider()
    {
        return $this->prepareDataForMethod('checkQuoteItemQty');
    }

    protected function prepareDataForMethod($methodName)
    {
        $variations = [];
        foreach ($this->getVariations() as $variation) {
            $stockItem = $this->getMockBuilder('Magento\CatalogInventory\Api\Data\StockItemInterface')
                ->disableOriginalConstructor()
                ->setMethods($this->stockItemMethods)
                ->getMockForAbstractClass();
            $stockItem->expects($this->any())->method('getSuppressCheckQtyIncrements')->willReturn(
                $variation['values']['_suppress_check_qty_increments_']
            );
            $stockItem->expects($this->any())->method('getIsSaleable')->willReturn(
                $variation['values']['_is_saleable_']
            );
            $stockItem->expects($this->any())->method('getOrderedItems')->willReturn(
                $variation['values']['_ordered_items_']
            );

            $stockItem->expects($this->any())->method('getProductName')->willReturn($variation['values']['_product_']);
            $stockItem->expects($this->any())->method('getIsChildItem')->willReturn(false);
            $stockItem->expects($this->any())->method('hasStockQty')->willReturn(false);
            $stockItem->expects($this->any())->method('setStockQty')->willReturnSelf();
            $stockItem->expects($this->any())->method('setOrderedItems')->willReturnSelf();
            $stockItem->expects($this->any())->method('getData')
                ->with('stock_qty')
                ->willReturn($variation['values']['_stock_qty_']);

            foreach ($this->stockItemMethods as $method) {
                $value = isset($variation['values'][$method]) ? $variation['values'][$method] : null;
                $stockItem->expects($this->any())->method($method)->willReturn($value);
            }
            $expectedResult = isset($variation['results'][$methodName]) ? $variation['results'][$methodName] : null;
            $variations[] = [
                'stockItem' => $stockItem,
                'expectedResult' => $expectedResult,
            ];
        }
        return $variations;
    }

    protected function getVariations()
    {
        $stockQty = 100;
        return [
            [
                'values' => [
                    'getIsInStock' => true,
                    'getQty' => $stockQty,
                    'getMinQty' => 0,
                    'getMinSaleQty' => 0,
                    'getMaxSaleQty' => 99,
                    'getNotifyStockQty' => 10,
                    'getManageStock' => true,
                    'getBackorders' => 1,
                    'getQtyIncrements' => 3,
                    '_stock_qty_' => $stockQty,
                    '_suppress_check_qty_increments_' => false,
                    '_is_saleable_' => true,
                    '_ordered_items_' => 0,
                    '_product_' => 'Test product Name',
                ],
                'results' => [
                    'verifyStock' => true,
                    'verifyNotification' => false,
                    'checkQty' => true,
                    'suggestQty' => 51,
                    'getStockQty' => $stockQty,
                    'checkQtyIncrements' => false,
                    'checkQuoteItemQty' => false,
                ],
            ],
            [
                'values' => [
                    'getIsInStock' => true,
                    'getQty' => $stockQty,
                    'getMinQty' => 60,
                    'getMinSaleQty' => 0,
                    'getMaxSaleQty' => 99,
                    'getNotifyStockQty' => 101,
                    'getManageStock' => true,
                    'getBackorders' => 3,
                    'getQtyIncrements' => 1,
                    '_stock_qty_' => $stockQty,
                    '_suppress_check_qty_increments_' => false,
                    '_is_saleable_' => true,
                    '_ordered_items_' => 0,
                    '_product_' => 'Test product Name',
                ],
                'results' => [
                    'verifyStock' => true,
                    'verifyNotification' => true,
                    'checkQty' => false,
                    'suggestQty' => 50.5,
                    'getStockQty' => $stockQty,
                    'checkQtyIncrements' => false,
                    'checkQuoteItemQty' => true,
                ]
            ],
            [
                'values' => [
                    'getIsInStock' => true,
                    'getQty' => null,
                    'getMinQty' => 60,
                    'getMinSaleQty' => 1,
                    'getMaxSaleQty' => 99,
                    'getNotifyStockQty' => 101,
                    'getManageStock' => true,
                    'getBackorders' => 0,
                    'getQtyIncrements' => 1,
                    '_stock_qty_' => null,
                    '_suppress_check_qty_increments_' => false,
                    '_is_saleable_' => true,
                    '_ordered_items_' => 0,
                    '_product_' => 'Test product Name',
                ],
                'results' => [
                    'verifyStock' => false,
                    'verifyNotification' => true,
                    'checkQty' => false,
                    'suggestQty' => 50.5,
                    'getStockQty' => null,
                    'checkQtyIncrements' => false,
                    'checkQuoteItemQty' => true,
                ]
            ]
        ];
    }

    /**
     * @param bool $isChildItem
     * @param string $expectedMsg
     * @dataProvider checkQtyIncrementsMsgDataProvider
     */
    public function testCheckQtyIncrementsMsg($isChildItem, $expectedMsg)
    {
        $qty = 1;
        $qtyIncrements = 5;
        $stockItem = $this->getMockBuilder('Magento\CatalogInventory\Api\Data\StockItemInterface')
            ->disableOriginalConstructor()
            ->setMethods($this->stockItemMethods)
            ->getMockForAbstractClass();
        $stockItem->expects($this->any())->method('getSuppressCheckQtyIncrements')->willReturn(false);
        $stockItem->expects($this->any())->method('getQtyIncrements')->willReturn($qtyIncrements);
        $stockItem->expects($this->any())->method('getIsChildItem')->willReturn($isChildItem);
        $stockItem->expects($this->any())->method('getProductName')->willReturn('Simple Product');
        $this->mathDivision->expects($this->any())->method('getExactDivision')->willReturn(1);

        $result = $this->stockStateProvider->checkQtyIncrements($stockItem, $qty);
        $this->assertTrue($result->getHasError());
        $this->assertEquals($expectedMsg, $result->getMessage()->render());
    }

    public function checkQtyIncrementsMsgDataProvider()
    {
        return [
            [true, 'You can buy Simple Product only in quantities of 5 at a time.'],
            [false, 'You can buy this product only in quantities of 5 at a time.'],
        ];
    }
}
