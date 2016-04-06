<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Catalog\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status as Status;

/**
 * Product Test
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 *
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $model;

    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemFactoryMock;

    /**
     * @var \Magento\Framework\Indexer\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryIndexerMock;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFlatProcessor;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productPriceProcessor;

    /**
     * @var Product\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productTypeInstanceMock;

    /**
     * @var Product\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionInstanceMock;

    /**
     * @var \Magento\Framework\Pricing\PriceInfo\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_priceInfoMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    private $store;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resource;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    /**
     * @var \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    private $website;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepository;

    /**
     * @var \Magento\Catalog\Helper\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_catalogProduct;

    /**
     * @var \Magento\Catalog\Model\Product\Image\Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $imageCache;

    /**
     * @var \Magento\Catalog\Model\Product\Image\CacheFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $imageCacheFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mediaGalleryEntryFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productLinkFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeValueFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkTypeProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mediaGalleryEntryConverterPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityCollectionProviderMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mediaConfig;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setUp()
    {
        $this->categoryIndexerMock = $this->getMockForAbstractClass('\Magento\Framework\Indexer\IndexerInterface');

        $this->moduleManager = $this->getMock(
            'Magento\Framework\Module\Manager',
            ['isEnabled'],
            [],
            '',
            false
        );
        $this->stockItemFactoryMock = $this->getMock(
            'Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->dataObjectHelperMock = $this->getMockBuilder('\Magento\Framework\Api\DataObjectHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->productFlatProcessor = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor',
            [],
            [],
            '',
            false
        );

        $this->_priceInfoMock = $this->getMock('Magento\Framework\Pricing\PriceInfo\Base', [], [], '', false);
        $this->productTypeInstanceMock = $this->getMock('Magento\Catalog\Model\Product\Type', [], [], '', false);
        $this->productPriceProcessor = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Price\Processor',
            [],
            [],
            '',
            false
        );

        $stateMock = $this->getMock('Magento\FrameworkApp\State', ['getAreaCode'], [], '', false);
        $stateMock->expects($this->any())
            ->method('getAreaCode')
            ->will($this->returnValue(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE));

        $this->eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $actionValidatorMock = $this->getMock(
            '\Magento\Framework\Model\ActionValidator\RemoveAction',
            [],
            [],
            '',
            false
        );
        $actionValidatorMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $cacheInterfaceMock = $this->getMock('Magento\Framework\App\CacheInterface');

        $contextMock = $this->getMock(
            '\Magento\Framework\Model\Context',
            ['getEventDispatcher', 'getCacheManager', 'getAppState', 'getActionValidator'], [], '', false
        );
        $contextMock->expects($this->any())->method('getAppState')->will($this->returnValue($stateMock));
        $contextMock->expects($this->any())
            ->method('getEventDispatcher')
            ->will($this->returnValue($this->eventManagerMock));
        $contextMock->expects($this->any())
            ->method('getCacheManager')
            ->will($this->returnValue($cacheInterfaceMock));
        $contextMock->expects($this->any())
            ->method('getActionValidator')
            ->will($this->returnValue($actionValidatorMock));

        $this->optionInstanceMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Option')
            ->setMethods(['setProduct', 'saveOptions', '__wakeup', '__sleep'])
            ->disableOriginalConstructor()->getMock();

        $optionFactory = $this->getMock(
            'Magento\Catalog\Model\Product\OptionFactory',
            ['create'],
            [],
            '',
            false
        );
        $optionFactory->expects($this->any())->method('create')->willReturn($this->optionInstanceMock);

        $this->resource = $this->getMockBuilder('Magento\Catalog\Model\ResourceModel\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->getMock();

        $this->store = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();

        $this->website = $this->getMockBuilder('\Magento\Store\Model\Website')
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->store));
        $storeManager->expects($this->any())
            ->method('getWebsite')
            ->will($this->returnValue($this->website));
        $this->indexerRegistryMock = $this->getMock('Magento\Framework\Indexer\IndexerRegistry', ['get'], [], '', false);
        $this->categoryRepository = $this->getMock('Magento\Catalog\Api\CategoryRepositoryInterface');

        $this->_catalogProduct = $this->getMock(
            'Magento\Catalog\Helper\Product',
            ['isDataForProductCategoryIndexerWasChanged'],
            [],
            '',
            false
        );

        $this->imageCache = $this->getMockBuilder('Magento\Catalog\Model\Product\Image\Cache')
            ->disableOriginalConstructor()
            ->getMock();
        $this->imageCacheFactory = $this->getMockBuilder('Magento\Catalog\Model\Product\Image\CacheFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->productLinkFactory = $this->getMockBuilder('Magento\Catalog\Api\Data\ProductLinkInterfaceFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->mediaGalleryEntryFactoryMock =
            $this->getMockBuilder('Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterfaceFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadataServiceMock = $this->getMock('\Magento\Catalog\Api\ProductAttributeRepositoryInterface');
        $this->attributeValueFactory = $this->getMockBuilder('Magento\Framework\Api\AttributeValueFactory')
            ->disableOriginalConstructor()->getMock();
        $this->linkTypeProviderMock = $this->getMock('Magento\Catalog\Model\Product\LinkTypeProvider',
            ['getLinkTypes'], [], '', false);
        $this->entityCollectionProviderMock = $this->getMock('Magento\Catalog\Model\ProductLink\CollectionProvider',
            ['getCollection'], [], '', false);

        $this->mediaGalleryEntryConverterPoolMock =
            $this->getMock(
                '\Magento\Catalog\Model\Product\Attribute\Backend\Media\EntryConverterPool',
                ['getConverterByMediaType'],
                [],
                '',
                false
            );

        $this->converterMock =
            $this->getMock(
                '\Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter',
                [],
                [],
                '',
                false
            );

        $this->mediaGalleryEntryConverterPoolMock->expects($this->any())->method('getConverterByMediaType')->willReturn(
            $this->converterMock
        );

        $this->mediaConfig = $this->getMock('Magento\Catalog\Model\Product\Media\Config', [], [], '', false);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Product',
            [
                'context' => $contextMock,
                'catalogProductType' => $this->productTypeInstanceMock,
                'productFlatIndexerProcessor' => $this->productFlatProcessor,
                'productPriceIndexerProcessor' => $this->productPriceProcessor,
                'catalogProductOptionFactory' => $optionFactory,
                'storeManager' => $storeManager,
                'resource' => $this->resource,
                'registry' => $this->registry,
                'moduleManager' => $this->moduleManager,
                'stockItemFactory' => $this->stockItemFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'indexerRegistry' => $this->indexerRegistryMock,
                'categoryRepository' => $this->categoryRepository,
                'catalogProduct' => $this->_catalogProduct,
                'imageCacheFactory' => $this->imageCacheFactory,
                'productLinkFactory' => $this->productLinkFactory,
                'mediaGalleryEntryFactory' => $this->mediaGalleryEntryFactoryMock,
                'metadataService' => $this->metadataServiceMock,
                'customAttributeFactory' => $this->attributeValueFactory,
                'entityCollectionProvider' => $this->entityCollectionProviderMock,
                'linkTypeProvider' => $this->linkTypeProviderMock,
                'mediaGalleryEntryConverterPool' => $this->mediaGalleryEntryConverterPoolMock,
                'catalogProductMediaConfig' => $this->mediaConfig,
                'data' => ['id' => 1]
            ]
        );

    }

    public function testGetAttributes()
    {
        $productType = $this->getMockBuilder('Magento\Catalog\Model\Product\Type\AbstractType')
            ->setMethods(['getSetAttributes'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->productTypeInstanceMock->expects($this->any())->method('factory')->will(
            $this->returnValue($productType)
        );
        $attribute = $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
            ->setMethods(['__wakeup', 'isInGroup'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $attribute->expects($this->any())->method('isInGroup')->will($this->returnValue(true));
        $productType->expects($this->any())->method('getSetAttributes')->will(
            $this->returnValue([$attribute])
        );
        $expect = [$attribute];
        $this->assertEquals($expect, $this->model->getAttributes(5));
        $this->assertEquals($expect, $this->model->getAttributes());
    }

    public function testGetStoreIds()
    {
        $expectedStoreIds = [1, 2, 3];
        $websiteIds = ['test'];
        $this->resource->expects($this->once())->method('getWebsiteIds')->will($this->returnValue($websiteIds));
        $this->website->expects($this->once())->method('getStoreIds')->will($this->returnValue($expectedStoreIds));
        $this->assertEquals($expectedStoreIds, $this->model->getStoreIds());
    }

    public function testGetStoreId()
    {
        $this->model->setStoreId(3);
        $this->assertEquals(3, $this->model->getStoreId());
        $this->model->unsStoreId();
        $this->store->expects($this->once())->method('getId')->will($this->returnValue(5));
        $this->assertEquals(5, $this->model->getStoreId());
    }

    public function testGetWebsiteIds()
    {
        $expected = ['test'];
        $this->resource->expects($this->once())->method('getWebsiteIds')->will($this->returnValue($expected));
        $this->assertEquals($expected, $this->model->getWebsiteIds());
    }

    public function testGetCategoryCollection()
    {
        $collection = $this->getMockBuilder('\Magento\Framework\Data\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource->expects($this->once())->method('getCategoryCollection')->will($this->returnValue($collection));
        $this->assertInstanceOf('\Magento\Framework\Data\Collection', $this->model->getCategoryCollection());
    }

    /**
     * @dataProvider getCategoryCollectionCollectionNullDataProvider
     */
    public function testGetCategoryCollectionCollectionNull($initCategoryCollection, $getIdResult, $productIdCached)
    {
        $product = $this->getMock(
            '\Magento\Catalog\Model\Product',
            [
                '_getResource',
                'setCategoryCollection',
                'getId',
            ],
            [],
            '',
            false
        );

        $abstractDbMock = $this->getMockBuilder('\Magento\Framework\Model\ResourceModel\Db\AbstractDb')
            ->disableOriginalConstructor()
            ->setMethods([
                'getCategoryCollection',
            ])
            ->getMockForAbstractClass();
        $getCategoryCollectionMock = $this->getMock(
            '\Magento\Framework\Data\Collection',
            [],
            [],
            '',
            false
        );
        $product
            ->expects($this->once())
            ->method('setCategoryCollection')
            ->with($getCategoryCollectionMock);
        $product
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($getIdResult);
        $abstractDbMock
            ->expects($this->once())
            ->method('getCategoryCollection')
            ->with($product)
            ->willReturn($getCategoryCollectionMock);
        $product
            ->expects($this->once())
            ->method('_getResource')
            ->willReturn($abstractDbMock);

        $this->setPropertyValue($product, 'categoryCollection', $initCategoryCollection);
        $this->setPropertyValue($product, '_productIdCached', $productIdCached);

        $result = $product->getCategoryCollection();

        $productIdCachedActual = $this->getPropertyValue($product, '_productIdCached', $productIdCached);
        $this->assertEquals($getIdResult, $productIdCachedActual);
        $this->assertEquals($initCategoryCollection, $result);
    }

    public function getCategoryCollectionCollectionNullDataProvider()
    {
        return [
            [
                '$initCategoryCollection' => null,
                '$getIdResult' => 'getIdResult value',
                '$productIdCached' => 'productIdCached value',
            ],
            [
                '$initCategoryCollection' => 'value',
                '$getIdResult' => 'getIdResult value',
                '$productIdCached' => 'not getIdResult value',
            ],
        ];
    }

    public function testSetCategoryCollection()
    {
        $collection = $this->getMockBuilder('\Magento\Framework\Data\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource->expects($this->once())->method('getCategoryCollection')->will($this->returnValue($collection));
        $this->assertSame($this->model->getCategoryCollection(), $this->model->getCategoryCollection());
    }

    public function testGetCategory()
    {
        $this->category->expects($this->any())->method('getId')->will($this->returnValue(10));
        $this->registry->expects($this->any())->method('registry')->will($this->returnValue($this->category));
        $this->categoryRepository->expects($this->any())->method('get')->will($this->returnValue($this->category));
        $this->assertInstanceOf('\Magento\Catalog\Model\Category', $this->model->getCategory());
    }

    public function testGetCategoryId()
    {
        $this->category->expects($this->once())->method('getId')->will($this->returnValue(10));

        $this->registry->expects($this->at(0))->method('registry');
        $this->registry->expects($this->at(1))->method('registry')->will($this->returnValue($this->category));
        $this->assertFalse($this->model->getCategoryId());
        $this->assertEquals(10, $this->model->getCategoryId());
    }

    public function testGetIdBySku()
    {
        $this->resource->expects($this->once())->method('getIdBySku')->will($this->returnValue(5));
        $this->assertEquals(5, $this->model->getIdBySku('someSku'));
    }

    public function testGetCategoryIds()
    {
        $this->model->lockAttribute('category_ids');
        $this->assertEquals([], $this->model->getCategoryIds());
    }

    public function testGetStatusInitial()
    {
        $this->assertEquals(Status::STATUS_ENABLED, $this->model->getStatus());
    }

    public function testGetStatus()
    {
        $this->model->setStatus(null);
        $this->assertEquals(Status::STATUS_ENABLED, $this->model->getStatus());
    }

    public function testIsInStock()
    {
        $this->model->setStatus(Status::STATUS_ENABLED);
        $this->assertTrue($this->model->isInStock());
    }

    public function testIndexerAfterDeleteCommitProduct()
    {
        $this->model->isDeleted(true);
        $this->categoryIndexerMock->expects($this->once())->method('reindexRow');
        $this->productFlatProcessor->expects($this->once())->method('reindexRow');
        $this->productPriceProcessor->expects($this->once())->method('reindexRow');
        $this->prepareCategoryIndexer();
        $this->model->afterDeleteCommit();
    }

    /**
     * @param $productChanged
     * @param $isScheduled
     * @param $productFlatCount
     * @param $categoryIndexerCount
     *
     * @dataProvider getProductReindexProvider
     */
    public function testReindex($productChanged, $isScheduled, $productFlatCount, $categoryIndexerCount)
    {
        $this->model->setData('entity_id', 1);
        $this->_catalogProduct->expects($this->once())
            ->method('isDataForProductCategoryIndexerWasChanged')
            ->willReturn($productChanged);
        if ($productChanged) {
            $this->indexerRegistryMock->expects($this->exactly($productFlatCount))
                ->method('get')
                ->with(\Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID)
                ->will($this->returnValue($this->categoryIndexerMock));
            $this->categoryIndexerMock->expects($this->any())
                ->method('isScheduled')
                ->will($this->returnValue($isScheduled));
            $this->categoryIndexerMock->expects($this->exactly($categoryIndexerCount))->method('reindexRow');
        }
        $this->productFlatProcessor->expects($this->exactly($productFlatCount))->method('reindexRow');
        $this->model->reindex();
    }

    public function getProductReindexProvider()
    {
        return array(
            'set 1' => [true, false, 1, 1],
            'set 2' => [true, true, 1, 0],
            'set 3' => [false, false, 1, 0]
        );
    }

    public function testPriceReindexCallback()
    {
        $this->model = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Product',
            [
                'catalogProductType' => $this->productTypeInstanceMock,
                'categoryIndexer' => $this->categoryIndexerMock,
                'productFlatIndexerProcessor' => $this->productFlatProcessor,
                'productPriceIndexerProcessor' => $this->productPriceProcessor,
                'catalogProductOption' => $this->optionInstanceMock,
                'resource' => $this->resource,
                'registry' => $this->registry,
                'categoryRepository' => $this->categoryRepository,
                'data' => []
            ]
        );
        $this->productPriceProcessor->expects($this->once())->method('reindexRow');
        $this->assertNull($this->model->priceReindexCallback());
    }

    /**
     * @dataProvider getIdentitiesProvider
     * @param array $expected
     * @param array $origData
     * @param array $data
     * @param bool $isDeleted
     */
    public function testGetIdentities($expected, $origData, $data, $isDeleted = false)
    {
        $this->model->setIdFieldName('id');
        if (is_array($origData)) {
            foreach ($origData as $key => $value) {
                $this->model->setOrigData($key, $value);
            }
        }
        foreach ($data as $key => $value) {
            $this->model->setData($key, $value);
        }
        $this->model->isDeleted($isDeleted);
        $this->assertEquals($expected, $this->model->getIdentities());
    }

    /**
     * @return array
     */
    public function getIdentitiesProvider()
    {
        return [
            'no changes' => [
                ['catalog_product_1'],
                ['id' => 1, 'name' => 'value', 'category_ids' => [1]],
                ['id' => 1, 'name' => 'value', 'category_ids' => [1]],
            ],
            'new product' => [
                ['catalog_product_1', 'catalog_category_product_1'],
                null,
                [
                    'id' => 1,
                    'name' => 'value',
                    'category_ids' => [1],
                    'affected_category_ids' => [1],
                    'is_changed_categories' => true
                ]
            ],
            'status and category change' => [
                [0 => 'catalog_product_1', 1 => 'catalog_category_product_1', 2 => 'catalog_category_product_2'],
                ['id' => 1, 'name' => 'value', 'category_ids' => [1], 'status' => 2],
                [
                    'id' => 1,
                    'name' => 'value',
                    'category_ids' => [2],
                    'status' => 1,
                    'affected_category_ids' => [1, 2],
                    'is_changed_categories' => true
                ],
            ],
            'status change only' => [
                [0 => 'catalog_product_1', 1 => 'catalog_category_product_7'],
                ['id' => 1, 'name' => 'value', 'category_ids' => [7], 'status' => 1],
                ['id' => 1, 'name' => 'value', 'category_ids' => [7], 'status' => 2],
            ],
            'status changed, category unassigned' => [
                [0 => 'catalog_product_1', 1 => 'catalog_category_product_5'],
                ['id' => 1, 'name' => 'value', 'category_ids' => [5], 'status' => 2],
                [
                    'id' => 1,
                    'name' => 'value',
                    'category_ids' => [],
                    'status' => 1,
                    'is_changed_categories' => true,
                    'affected_category_ids' => [5]
                ],
            ],
            'no status changes' => [
                [0 => 'catalog_product_1'],
                ['id' => 1, 'name' => 'value', 'category_ids' => [1], 'status' => 1],
                ['id' => 1, 'name' => 'value', 'category_ids' => [1], 'status' => 1],
            ]
        ];
    }

    public function testStatusAfterLoad()
    {
        $this->resource->expects($this->once())->method('load')->with($this->model, 1, null);
        $this->eventManagerMock->expects($this->exactly(4))->method('dispatch');
        $this->model->load(1);
        $this->assertEquals(
            Status::STATUS_ENABLED,
            $this->model->getData(\Magento\Catalog\Model\Product::STATUS)
        );
        $this->assertFalse($this->model->hasDataChanges());
        $this->model->setStatus(Status::STATUS_DISABLED);
        $this->assertTrue($this->model->hasDataChanges());
    }

    /**
     * Test retrieving price Info
     */
    public function testGetPriceInfo()
    {
        $this->productTypeInstanceMock->expects($this->once())
            ->method('getPriceInfo')
            ->with($this->equalTo($this->model))
            ->will($this->returnValue($this->_priceInfoMock));
        $this->assertEquals($this->model->getPriceInfo(), $this->_priceInfoMock);
    }

    /**
     * Test for set qty
     */
    public function testSetQty()
    {
        $this->productTypeInstanceMock->expects($this->exactly(2))
            ->method('getPriceInfo')
            ->with($this->equalTo($this->model))
            ->will($this->returnValue($this->_priceInfoMock));

        //initialize the priceInfo field
        $this->model->getPriceInfo();
        //Calling setQty will reset the priceInfo field
        $this->assertEquals($this->model, $this->model->setQty(1));
        //Call the setQty method with the same qty, getPriceInfo should not be called this time
        $this->assertEquals($this->model, $this->model->setQty(1));
        $this->assertEquals($this->model->getPriceInfo(), $this->_priceInfoMock);
    }

    /**
     * Test reload PriceInfo
     */
    public function testReloadPriceInfo()
    {
        $this->productTypeInstanceMock->expects($this->exactly(2))
            ->method('getPriceInfo')
            ->with($this->equalTo($this->model))
            ->will($this->returnValue($this->_priceInfoMock));
        $this->assertEquals($this->_priceInfoMock, $this->model->getPriceInfo());
        $this->assertEquals($this->_priceInfoMock, $this->model->reloadPriceInfo());
    }

    /**
     * Test for get qty
     */
    public function testGetQty()
    {
        $this->model->setQty(1);
        $this->assertEquals(1, $this->model->getQty());
    }

    /**
     *  Test for `save` method
     */
    public function testSave()
    {
        $this->imageCache->expects($this->once())
            ->method('generate')
            ->with($this->model);
        $this->imageCacheFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->imageCache);

        $this->model->setIsDuplicate(false);
        $this->configureSaveTest();
        $this->optionInstanceMock->expects($this->any())->method('setProduct')->will($this->returnSelf());
        $this->optionInstanceMock->expects($this->once())->method('saveOptions')->will($this->returnSelf());
        $this->model->beforeSave();
        $this->model->afterSave();
    }

    /**
     *  Test for `save` method for duplicated product
     */
    public function testSaveAndDuplicate()
    {
        $this->imageCache->expects($this->once())
            ->method('generate')
            ->with($this->model);
        $this->imageCacheFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->imageCache);

        $this->model->setIsDuplicate(true);
        $this->configureSaveTest();
        $this->model->beforeSave();
        $this->model->afterSave();
    }

    public function testGetIsSalableConfigurable()
    {
        $typeInstanceMock = $this->getMock(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable', ['getIsSalable'], [], '', false);

        $typeInstanceMock
            ->expects($this->atLeastOnce())
            ->method('getIsSalable')
            ->willReturn(true);

        $this->model->setTypeInstance($typeInstanceMock);

        self::assertTrue($this->model->getIsSalable());
    }

    public function testGetIsSalableSimple()
    {
        $typeInstanceMock =
            $this->getMock('Magento\Catalog\Model\Product\Type\Simple', ['isSalable'], [], '', false);
        $typeInstanceMock
            ->expects($this->atLeastOnce())
            ->method('isSalable')
            ->willReturn(true);

        $this->model->setTypeInstance($typeInstanceMock);

        self::assertTrue($this->model->getIsSalable());
    }

    public function testGetIsSalableHasDataIsSaleable()
    {
        $typeInstanceMock = $this->getMock('Magento\Catalog\Model\Product\Type\Simple', [], [], '', false);

        $this->model->setTypeInstance($typeInstanceMock);
        $this->model->setData('is_saleable', true);
        $this->model->setData('is_salable', false);

        self::assertTrue($this->model->getIsSalable());
    }

    /**
     * Configure environment for `testSave` and `testSaveAndDuplicate` methods
     *
     * @return array
     */
    protected function configureSaveTest()
    {
        $productTypeMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Type\Simple')
            ->disableOriginalConstructor()->setMethods(['beforeSave', 'save'])->getMock();
        $productTypeMock->expects($this->once())->method('beforeSave')->will($this->returnSelf());
        $productTypeMock->expects($this->once())->method('save')->will($this->returnSelf());

        $this->productTypeInstanceMock->expects($this->once())->method('factory')->with($this->model)
            ->will($this->returnValue($productTypeMock));

        $this->model->getResource()->expects($this->any())->method('addCommitCallback')->will($this->returnSelf());
        $this->model->getResource()->expects($this->any())->method('commit')->will($this->returnSelf());
    }

    /**
     * Run test fromArray method
     *
     * @return void
     */
    public function testFromArray()
    {
        $data = [
            'stock_item' => ['stock-item-data'],
        ];

        $stockItemMock = $this->getMockForAbstractClass(
            'Magento\Framework\Api\AbstractSimpleObject',
            [],
            '',
            false,
            true,
            true,
            ['setProduct']
        );

        $this->moduleManager->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_CatalogInventory')
            ->will($this->returnValue(true));
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($stockItemMock, $data['stock_item'], '\Magento\CatalogInventory\Api\Data\StockItemInterface')
            ->will($this->returnSelf());
        $this->stockItemFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($stockItemMock));
        $stockItemMock->expects($this->once())->method('setProduct')->with($this->model);

        $this->assertEquals($this->model, $this->model->fromArray($data));
    }

    protected function prepareCategoryIndexer()
    {
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID)
            ->will($this->returnValue($this->categoryIndexerMock));
    }

    /**
     *  Test for getProductLinks()
     */
    public function testGetProductLinks()
    {
        $linkTypes = ['related' => 1, 'upsell' => 4, 'crosssell' => 5, 'associated' => 3];
        $this->linkTypeProviderMock->expects($this->once())
            ->method('getLinkTypes')
            ->willReturn($linkTypes);

        $inputRelatedLink = $this->objectManagerHelper->getObject('Magento\Catalog\Model\ProductLink\Link');
        $inputRelatedLink->setSku("Simple Product 1");
        $inputRelatedLink->setLinkType("related");
        $inputRelatedLink->setData("sku", "Simple Product 2");
        $inputRelatedLink->setData("type", "simple");
        $inputRelatedLink->setPosition(0);

        $outputRelatedLink = $this->objectManagerHelper->getObject('Magento\Catalog\Model\ProductLink\Link');
        $outputRelatedLink->setSku("Simple Product 1");
        $outputRelatedLink->setLinkType("related");
        $outputRelatedLink->setLinkedProductSku("Simple Product 2");
        $outputRelatedLink->setLinkedProductType("simple");
        $outputRelatedLink->setPosition(0);

        $this->entityCollectionProviderMock->expects($this->at(0))
            ->method('getCollection')
            ->with($this->model, 'related')
            ->willReturn([$inputRelatedLink]);
        $this->entityCollectionProviderMock->expects($this->at(1))
            ->method('getCollection')
            ->with($this->model, 'upsell')
            ->willReturn([]);
        $this->entityCollectionProviderMock->expects($this->at(2))
            ->method('getCollection')
            ->with($this->model, 'crosssell')
            ->willReturn([]);
        $this->entityCollectionProviderMock->expects($this->at(3))
            ->method('getCollection')
            ->with($this->model, 'associated')
            ->willReturn([]);

        $expectedOutput = [$outputRelatedLink];
        $typeInstanceMock = $this->getMock(
            'Magento\ConfigurableProduct\Model\Product\Type\Simple', ["getSku"], [], '', false);
        $typeInstanceMock
            ->expects($this->atLeastOnce())
            ->method('getSku')
            ->willReturn("Simple Product 1");
        $this->model->setTypeInstance($typeInstanceMock);

        $productLink1 = $this->objectManagerHelper->getObject('Magento\Catalog\Model\ProductLink\Link');
        $this->productLinkFactory->expects($this->at(0))
            ->method('create')
            ->willReturn($productLink1);

        $links = $this->model->getProductLinks();
        $this->assertEquals($links, $expectedOutput);
    }

    /**
     *  Test for setProductLinks()
     */
    public function testSetProductLinks()
    {
        $link = $this->objectManagerHelper->getObject('Magento\Catalog\Model\ProductLink\Link');
        $link->setSku("Simple Product 1");
        $link->setLinkType("upsell");
        $link->setLinkedProductSku("Simple Product 2");
        $link->setLinkedProductType("simple");
        $link->setPosition(0);
        $productLinks = [$link];
        $this->model->setProductLinks($productLinks);
        $this->assertEquals($productLinks, $this->model->getProductLinks());
    }

    /**
     * Set up two media attributes: image and small_image
     */
    protected function setupMediaAttributes()
    {
        $productType = $this->getMockBuilder('Magento\Catalog\Model\Product\Type\AbstractType')
            ->setMethods(['getSetAttributes'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->productTypeInstanceMock->expects($this->any())->method('factory')->will(
            $this->returnValue($productType)
        );

        $frontendMock = $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend')
            ->disableOriginalConstructor()
            ->setMethods(['getInputType'])
            ->getMockForAbstractClass();
        $frontendMock->expects($this->any())->method('getInputType')->willReturn('media_image');
        $attributeImage = $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
            ->setMethods(['__wakeup', 'getFrontend', 'getAttributeCode'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $attributeImage->expects($this->any())
            ->method('getFrontend')
            ->willReturn($frontendMock);
        $attributeImage->expects($this->any())->method('getAttributeCode')->willReturn('image');
        $attributeSmallImage = $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
            ->setMethods(['__wakeup', 'getFrontend', 'getAttributeCode'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $attributeSmallImage->expects($this->any())
            ->method('getFrontend')
            ->willReturn($frontendMock);
        $attributeSmallImage->expects($this->any())->method('getAttributeCode')->willReturn('small_image');

        $productType->expects($this->any())->method('getSetAttributes')->will(
            $this->returnValue(['image' => $attributeImage, 'small_image' => $attributeSmallImage])
        );

        return [$attributeImage, $attributeSmallImage];
    }

    public function getMediaAttributes()
    {
        $expected = [];
        $mediaAttributes = $this->setupMediaAttributes();
        foreach ($mediaAttributes as $mediaAttribute) {
            $expected[$mediaAttribute->getAttributeCode()] = $mediaAttribute;
        }
        $this->assertEquals($expected, $this->model->getMediaAttributes());
    }

    public function testGetMediaAttributeValues()
    {
        $this->mediaConfig->expects($this->once())->method('getMediaAttributeCodes')
            ->willReturn(['image', 'small_image']);
        $this->model->setData('image', 'imageValue');
        $this->model->setData('small_image', 'smallImageValue');

        $expectedMediaAttributeValues = [
            'image' => 'imageValue',
            'small_image' => 'smallImageValue',
        ];
        $this->assertEquals($expectedMediaAttributeValues, $this->model->getMediaAttributeValues());
    }

    public function testGetGalleryAttributeBackendNon()
    {
        $this->setupMediaAttributes();
        $this->assertNull($this->model->getGalleryAttributeBackend());
    }

    public function testGetMediaGalleryEntriesNone()
    {
        $this->assertNull($this->model->getMediaGalleryEntries());
    }

    public function testGetMediaGalleryEntries()
    {
        $this->setupMediaAttributes();
        $this->model->setData('image', 'imageFile.jpg');
        $this->model->setData('small_image', 'smallImageFile.jpg');

        $mediaEntries = [
            'images' => [
                [
                    'value_id' => 1,
                    'file' => 'imageFile.jpg',
                    'media_type' => 'image',
                ],
                [
                    'value_id' => 2,
                    'file' => 'smallImageFile.jpg',
                    'media_type' => 'image',
                ],
            ]
        ];
        $this->model->setData('media_gallery', $mediaEntries);

        $entry1 =
            $this->getMock('\Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface', [], [], '', false);
        $entry2 =
            $this->getMock('\Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface', [], [], '', false);

        $this->converterMock->expects($this->exactly(2))->method('convertTo')->willReturnOnConsecutiveCalls(
            $entry1,
            $entry2
        );

        $this->assertEquals([$entry1, $entry2], $this->model->getMediaGalleryEntries());
    }

    public function testSetMediaGalleryEntries()
    {
        $expectedResult = [
            'images' => [
                [
                    'value_id' => 1,
                    'file' => 'file1.jpg',
                    'label' => 'label_text',
                    'position' => 4,
                    'disabled' => false,
                    'types' => ['image'],
                    'content' => [
                        'data' => [
                            ImageContentInterface::NAME => 'product_image',
                            ImageContentInterface::TYPE => 'image/jpg',
                            ImageContentInterface::BASE64_ENCODED_DATA => 'content_data'
                        ]
                    ],
                    'media_type' => 'image'
                ]
            ],
        ];

        $entryMock = $this->getMockBuilder('\Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface')
                          ->setMethods(
                              [
                                  'getId',
                                  'getFile',
                                  'getLabel',
                                  'getPosition',
                                  'isDisabled',
                                  'types',
                                  'getContent',
                                  'getMediaType'
                              ]
                          )
                          ->getMockForAbstractClass();

        $result = [
            'value_id' => 1,
            'file' => 'file1.jpg',
            'label' => 'label_text',
            'position' => 4,
            'disabled' => false,
            'types' => ['image'],
            'content' => [
                'data' => [
                    ImageContentInterface::NAME => 'product_image',
                    ImageContentInterface::TYPE => 'image/jpg',
                    ImageContentInterface::BASE64_ENCODED_DATA => 'content_data'
                ]
            ],
            'media_type' => 'image'
        ];

        $this->converterMock->expects($this->once())->method('convertFrom')->with($entryMock)->willReturn($result);

        $this->model->setMediaGalleryEntries([$entryMock]);
        $this->assertEquals($expectedResult, $this->model->getMediaGallery());
    }

    public function testGetCustomAttributes()
    {
        $priceCode = 'price';
        $colorAttributeCode = 'color';
        $interfaceAttribute = $this->getMock('\Magento\Framework\Api\MetadataObjectInterface');
        $interfaceAttribute->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn($priceCode);
        $colorAttribute = $this->getMock('\Magento\Framework\Api\MetadataObjectInterface');
        $colorAttribute->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn($colorAttributeCode);
        $customAttributesMetadata = [$interfaceAttribute, $colorAttribute];

        $this->metadataServiceMock->expects($this->once())
            ->method('getCustomAttributesMetadata')
            ->willReturn($customAttributesMetadata);
        $this->model->setData($priceCode, 10);

        //The color attribute is not set, expect empty custom attribute array
        $this->assertEquals([], $this->model->getCustomAttributes());

        //Set the color attribute;
        $this->model->setData($colorAttributeCode, "red");
        $attributeValue = new \Magento\Framework\Api\AttributeValue();
        $attributeValue2 = new \Magento\Framework\Api\AttributeValue();
        $this->attributeValueFactory->expects($this->exactly(2))->method('create')
            ->willReturnOnConsecutiveCalls($attributeValue, $attributeValue2);
        $this->assertEquals(1, count($this->model->getCustomAttributes()));
        $this->assertNotNull($this->model->getCustomAttribute($colorAttributeCode));
        $this->assertEquals("red", $this->model->getCustomAttribute($colorAttributeCode)->getValue());

        //Change the attribute value, should reflect in getCustomAttribute
        $this->model->setData($colorAttributeCode, "blue");
        $this->assertEquals(1, count($this->model->getCustomAttributes()));
        $this->assertNotNull($this->model->getCustomAttribute($colorAttributeCode));
        $this->assertEquals("blue", $this->model->getCustomAttribute($colorAttributeCode)->getValue());
    }

    /**
     * @return array
     */
    public function priceDataProvider()
    {
        return [
            'receive empty array' => [[]],
            'receive null' => [null],
            'receive non-empty array' => [['non-empty', 'array', 'of', 'values']]
        ];
    }

    public function testGetOptions()
    {
        $optionInstanceMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Option')
            ->disableOriginalConstructor()
            ->getMock();
        $optionFactory = $this->getMock(
            'Magento\Catalog\Model\Product\OptionFactory',
            ['create'],
            [],
            '',
            false
        );
        $optionFactory->expects($this->any())->method('create')->willReturn($optionInstanceMock);
        $joinProcessorMock = $this->getMockBuilder('Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        // the productModel
        $productModel = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Product',
            [
                'catalogProductOptionFactory' => $optionFactory,
                'joinProcessor' => $joinProcessorMock
            ]
        );
        $productModel->setHasOptions(true);

        $option1Id = 2;
        $optionMock1 = $this->getMockBuilder('\Magento\Catalog\Model\Product\Option')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'setProduct'])
            ->getMock();
        $optionMock1->expects($this->once())
            ->method('getId')
            ->willReturn($option1Id);
        $optionMock1->expects($this->once())
            ->method('setProduct')
            ->with($productModel)
            ->willReturn($option1Id);

        $option2Id = 3;
        $optionMock2 = $this->getMockBuilder('\Magento\Catalog\Model\Product\Option')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'setProduct'])
            ->getMock();
        $optionMock2->expects($this->once())
            ->method('getId')
            ->willReturn($option2Id);
        $optionMock1->expects($this->once())
            ->method('setProduct')
            ->with($productModel)
            ->willReturn($option1Id);
        $optionColl = $this->objectManagerHelper->getCollectionMock(
            'Magento\Catalog\Model\ResourceModel\Product\Option\Collection',
            [$optionMock1, $optionMock2]
        );

        $optionInstanceMock->expects($this->once())
            ->method('getProductOptionCollection')
            ->with($productModel)
            ->willReturn($optionColl);

        $joinProcessorMock->expects($this->once())
            ->method('process')
            ->with($this->isInstanceOf('Magento\Catalog\Model\ResourceModel\Product\Option\Collection'));

        $expectedOptions = [
            $option1Id => $optionMock1,
            $option2Id => $optionMock2
        ];
        $this->assertEquals($expectedOptions, $productModel->getOptions());

        //Calling the method again, empty options array will be returned
        $productModel->setOptions([]);
        $this->assertEquals([], $productModel->getOptions());
    }

    /**
     * @param $object
     * @param $property
     * @param $value
     */
    protected function setPropertyValue(&$object, $property, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
        return $object;
    }

    /**
     * @param $object
     * @param $property
     */
    protected function getPropertyValue(&$object, $property)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    public function testGetFinalPrice()
    {
        $finalPrice = 11;
        $qty = 1;
        $this->model->setQty($qty);
        $productTypePriceMock = $this->getMock(
            'Magento\Catalog\Model\Product\Type\Price',
            ['getFinalPrice'],
            [],
            '',
            false
        );

        $productTypePriceMock->expects($this->any())
            ->method('getFinalPrice')
            ->with($qty, $this->model)
            ->will($this->returnValue($finalPrice));

        $this->productTypeInstanceMock->expects($this->any())
            ->method('priceFactory')
            ->with($this->model->getTypeId())
            ->will($this->returnValue($productTypePriceMock));

        $this->assertEquals($finalPrice, $this->model->getFinalPrice($qty));
        $this->model->setFinalPrice(9.99);
    }

    public function testGetFinalPricePreset()
    {
        $finalPrice = 9.99;
        $qty = 1;
        $this->model->setQty($qty);
        $this->model->setFinalPrice($finalPrice);
        $this->productTypeInstanceMock->expects($this->never())->method('priceFactory');
        $this->assertEquals($finalPrice, $this->model->getFinalPrice($qty));
    }

    public function testGetTypeId()
    {
        $productType = $this->getMockBuilder('Magento\Catalog\Model\Product\Type\Virtual')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->productTypeInstanceMock->expects($this->exactly(2))->method('factory')->will(
            $this->returnValue($productType)
        );

        $this->model->getTypeInstance();
        $this->model->setTypeId('typeId');
        $this->model->getTypeInstance();
    }
}
