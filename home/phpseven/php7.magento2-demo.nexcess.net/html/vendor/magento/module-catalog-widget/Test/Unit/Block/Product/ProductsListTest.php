<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogWidget\Test\Unit\Block\Product;

use \Magento\CatalogWidget\Block\Product\ProductsList;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Catalog\Model\Product\Visibility;

/**
 * Class ProductsListTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductsListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogWidget\Block\Product\ProductsList
     */
    protected $productsList;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $visibility;

    /**
     * @var \Magento\Framework\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContext;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $builder;

    /**
     * @var \Magento\CatalogWidget\Model\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rule;

    /**
     * @var \Magento\Widget\Helper\Conditions|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $widgetConditionsHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $design;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    protected function setUp()
    {
        $this->collectionFactory =
            $this->getMockBuilder('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()->getMock();
        $this->visibility = $this->getMockBuilder('Magento\Catalog\Model\Product\Visibility')
            ->setMethods(['getVisibleInCatalogIds'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->httpContext = $this->getMock('Magento\Framework\App\Http\Context');
        $this->builder = $this->getMock('Magento\Rule\Model\Condition\Sql\Builder', [], [], '', false);
        $this->rule = $this->getMock('Magento\CatalogWidget\Model\Rule', [], [], '', false);
        $this->widgetConditionsHelper = $this->getMock('Magento\Widget\Helper\Conditions');
        $this->storeManager = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->design = $this->getMock('\Magento\Framework\View\DesignInterface');

        $objectManagerHelper = new ObjectManagerHelper($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento\CatalogWidget\Block\Product\ProductsList',
            [
                'productCollectionFactory' => $this->collectionFactory,
                'catalogProductVisibility' => $this->visibility,
                'httpContext' => $this->httpContext,
                'sqlBuilder' => $this->builder,
                'rule' => $this->rule,
                'conditionsHelper' => $this->widgetConditionsHelper,
                'storeManager' => $this->storeManager,
                'design' => $this->design
            ]
        );
        $this->request = $arguments['context']->getRequest();
        $this->layout = $arguments['context']->getLayout();

        $this->productsList = $objectManagerHelper->getObject(
            'Magento\CatalogWidget\Block\Product\ProductsList',
            $arguments
        );
    }

    public function testGetCacheKeyInfo()
    {
        $store = $this->getMockBuilder('\Magento\Store\Model\Store')
            ->disableOriginalConstructor()->setMethods(['getId'])->getMock();
        $store->expects($this->once())->method('getId')->willReturn(1);
        $this->storeManager->expects($this->once())->method('getStore')->willReturn($store);

        $theme = $this->getMock('\Magento\Framework\View\Design\ThemeInterface');
        $theme->expects($this->once())->method('getId')->willReturn('blank');
        $this->design->expects($this->once())->method('getDesignTheme')->willReturn($theme);

        $this->httpContext->expects($this->once())->method('getValue')->willReturn('context_group');
        $this->productsList->setData('conditions', 'some_serialized_conditions');

        $this->request->expects($this->once())->method('getParam')->with('np')->willReturn(1);

        $cacheKey = ['CATALOG_PRODUCTS_LIST_WIDGET', 1, 'blank', 'context_group', 1, 5, 'some_serialized_conditions'];
        $this->assertEquals($cacheKey, $this->productsList->getCacheKeyInfo());
    }

    public function testGetProductPriceHtml()
    {
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->once())->method('getId')->willReturn(1);

        $priceRenderer = $this->getMockBuilder('\Magento\Framework\Pricing\Render')
            ->setMethods(['render'])
            ->disableOriginalConstructor()
            ->getMock();
        $priceRenderer->expects($this->once())
            ->method('render')
            ->with('final_price', $product, [
                'include_container' => false,
                'display_minimal_price' => false,
                'zone' => 'item_list',
                'price_id' => 'old-price-1-some-price-type'
            ])
            ->willReturn('<html>');
        $this->layout->expects($this->once())->method('getBlock')->willReturn($priceRenderer);

        $this->assertEquals('<html>', $this->productsList->getProductPriceHtml(
            $product,
            'some-price-type',
            \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
            [
                'include_container' => false,
                'display_minimal_price' => false
            ]
        ));
    }

    public function testGetPagerHtmlEmpty()
    {
        $this->assertEquals('', $this->productsList->getPagerHtml());
    }

    public function testGetPagerHtml()
    {
        $collection = $this->getMockBuilder('\Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->setMethods(['getSize'])
            ->disableOriginalConstructor()
            ->getMock();
        $collection->expects($this->once())->method('getSize')->willReturn(3);

        $this->productsList->setData('show_pager', true);
        $this->productsList->setData('products_per_page', 2);
        $this->productsList->setData('product_collection', $collection);

        $pagerBlock = $this->getMockBuilder('Magento\Catalog\Block\Product\Widget\Html\Pager')
            ->setMethods([
                'toHtml',
                'setUseContainer',
                'setShowAmounts',
                'setShowPerPage',
                'setPageVarName',
                'setLimit',
                'setTotalLimit',
                'setCollection',
            ])->disableOriginalConstructor()->getMock();

        $pagerBlock->expects($this->once())->method('setUseContainer')->willReturnSelf();
        $pagerBlock->expects($this->once())->method('setShowAmounts')->willReturnSelf();
        $pagerBlock->expects($this->once())->method('setShowPerPage')->willReturnSelf();
        $pagerBlock->expects($this->once())->method('setPageVarName')->willReturnSelf();
        $pagerBlock->expects($this->once())->method('setLimit')->willReturnSelf();
        $pagerBlock->expects($this->once())->method('setTotalLimit')->willReturnSelf();
        $pagerBlock->expects($this->once())->method('setCollection')->with($collection)->willReturnSelf();

        $pagerBlock->expects($this->once())->method('toHtml')->willReturn('<pager_html>');
        $this->layout->expects($this->once())->method('createBlock')->willReturn($pagerBlock);
        $this->assertEquals('<pager_html>', $this->productsList->getPagerHtml());
    }

    /**
     * Test public `createCollection` method and protected `getPageSize` method via `createCollection`
     *
     * @param bool $pagerEnable
     * @param int $productsCount
     * @param int $productsPerPage
     * @param int $expectedPageSize
     * @dataProvider createCollectionDataProvider
     */
    public function testCreateCollection($pagerEnable, $productsCount, $productsPerPage, $expectedPageSize)
    {
        $this->visibility->expects($this->once())->method('getVisibleInCatalogIds')
            ->willReturn([Visibility::VISIBILITY_IN_CATALOG, Visibility::VISIBILITY_BOTH]);
        $collection = $this->getMockBuilder('\Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->setMethods([
                'setVisibility',
                'addMinimalPrice',
                'addFinalPrice',
                'addTaxPercents',
                'addAttributeToSelect',
                'addUrlRewrite',
                'addStoreFilter',
                'setPageSize',
                'setCurPage',
            ])->disableOriginalConstructor()
            ->getMock();
        $collection->expects($this->once())->method('setVisibility')
            ->with([Visibility::VISIBILITY_IN_CATALOG, Visibility::VISIBILITY_BOTH])
            ->willReturnSelf();
        $collection->expects($this->once())->method('addMinimalPrice')->willReturnSelf();
        $collection->expects($this->once())->method('addFinalPrice')->willReturnSelf();
        $collection->expects($this->once())->method('addTaxPercents')->willReturnSelf();
        $collection->expects($this->once())->method('addAttributeToSelect')->willReturnSelf();
        $collection->expects($this->once())->method('addUrlRewrite')->willReturnSelf();
        $collection->expects($this->once())->method('addStoreFilter')->willReturnSelf();
        $collection->expects($this->once())->method('setPageSize')->with($expectedPageSize)->willReturnSelf();
        $collection->expects($this->once())->method('setCurPage')->willReturnSelf();

        $this->collectionFactory->expects($this->once())->method('create')->willReturn($collection);
        $this->productsList->setData('conditions_encoded', 'some_serialized_conditions');

        $conditions = $this->getMockBuilder('\Magento\Rule\Model\Condition\Combine')
            ->setMethods(['collectValidatedAttributes'])
            ->disableOriginalConstructor()
            ->getMock();
        $conditions->expects($this->once())->method('collectValidatedAttributes')
            ->with($collection)
            ->willReturnSelf();

        $this->builder->expects($this->once())->method('attachConditionToCollection')
            ->with($collection, $conditions)
            ->willReturnSelf();

        $this->rule->expects($this->once())->method('loadPost')->willReturnSelf();
        $this->rule->expects($this->once())->method('getConditions')->willReturn($conditions);


        if ($productsPerPage) {
            $this->productsList->setData('products_per_page', $productsPerPage);
        } else {
            $this->productsList->unsetData('products_per_page');
        }

        $this->productsList->setData('show_pager', $pagerEnable);
        $this->productsList->setData('products_count', $productsCount);

        $this->assertSame($collection, $this->productsList->createCollection());
    }

    public function createCollectionDataProvider()
    {
        return [
            [true, 1, null, 5],
            [true, 5, null, 5],
            [true, 10, null, 5],
            [true, 1, 2, 2],
            [true, 5, 3, 3],
            [true, 10, 7, 7],
            [false, 1, null, 1],
            [false, 3, null, 3],
            [false, 5, null, 5],
            [false, 1, 3, 1],
            [false, 3, 5, 3],
            [false, 5, 10, 5]
        ];
    }

    public function testGetProductsCount()
    {
        $this->assertEquals(10, $this->productsList->getProductsCount());
        $this->productsList->setProductsCount(2);
        $this->assertEquals(2, $this->productsList->getProductsCount());
    }

    public function testGetProductsPerPage()
    {
        $this->productsList->setData('products_per_page', 2);
        $this->assertEquals(2, $this->productsList->getProductsPerPage());
    }

    public function testGetDefaultProductsPerPage()
    {
        $this->assertEquals(ProductsList::DEFAULT_PRODUCTS_PER_PAGE, $this->productsList->getProductsPerPage());
    }

    public function testShowPager()
    {
        $this->assertEquals(false, $this->productsList->showPager());
        $this->productsList->setData('show_pager', true);
        $this->assertEquals(true, $this->productsList->showPager());
    }

    public function testGetIdentities()
    {
        $this->assertEquals([\Magento\Catalog\Model\Product::CACHE_TAG], $this->productsList->getIdentities());
    }

    public function testGetTitle()
    {
        $this->assertEmpty($this->productsList->getTitle());
    }

    public function testGetNonDefaultTitle()
    {
        $this->productsList->setTitle('Custom Title');
        $this->assertEquals('Custom Title', $this->productsList->getTitle());
    }

    public function testScope()
    {
        $this->assertFalse($this->productsList->isScopePrivate());
    }
}
