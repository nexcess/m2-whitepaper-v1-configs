<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Customer\Test\Unit\Model;

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\Customer;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Indexer\IndexerInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Attribute
     */
    protected $attribute;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Api\AttributeValueFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeValueFactoryMock;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Eav\Model\Entity\TypeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeFactoryMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Framework\Validator\UniversalFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $universalFactoryMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $timezoneMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var \Magento\Catalog\Model\Product\ReservedAttributeList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reservedAttributeListMock;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolverMock;

    /**
     * @var \Magento\Framework\App\CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventDispatcher;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeOptionFactoryMock;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $indexerRegistryMock;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeFormatter;

    /**
     * Test method
     */
    protected function setUp()
    {
        $this->contextMock = $this->getMockBuilder('Magento\Framework\Model\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->getMock();
        $this->extensionAttributesFactory = $this->getMockBuilder('Magento\Framework\Api\ExtensionAttributesFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeValueFactoryMock = $this->getMockBuilder('Magento\Framework\Api\AttributeValueFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder('Magento\Eav\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->typeFactoryMock = $this->getMockBuilder('Magento\Eav\Model\Entity\TypeFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->getMock();
        $this->helperMock = $this->getMockBuilder('Magento\Eav\Model\ResourceModel\Helper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->universalFactoryMock = $this->getMockBuilder('Magento\Framework\Validator\UniversalFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeOptionFactoryMock =
            $this->getMockBuilder('Magento\Eav\Api\Data\AttributeOptionInterfaceFactory')
                ->disableOriginalConstructor()
                ->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder('Magento\Framework\Reflection\DataObjectProcessor')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder('Magento\Framework\Api\DataObjectHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->timezoneMock = $this->getMockBuilder('Magento\Framework\Stdlib\DateTime\TimezoneInterface')
            ->getMock();
        $this->reservedAttributeListMock = $this->getMockBuilder('Magento\Catalog\Model\Product\ReservedAttributeList')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resolverMock = $this->getMockBuilder('Magento\Framework\Locale\ResolverInterface')
            ->getMock();
        $this->dateTimeFormatter = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface');

        $this->resourceMock = $this->getMockBuilder('Magento\Framework\Model\ResourceModel\AbstractResource')
            ->setMethods(['_construct', 'getConnection', 'getIdFieldName', 'saveInSetIncluding'])
            ->getMockForAbstractClass();
        $this->cacheManager = $this->getMockBuilder('Magento\Framework\App\CacheInterface')
            ->getMock();
        $this->eventDispatcher = $this->getMockBuilder('Magento\Framework\Event\ManagerInterface')
            ->getMock();

        $this->contextMock
            ->expects($this->any())
            ->method('getCacheManager')
            ->willReturn($this->cacheManager);
        $this->contextMock
            ->expects($this->any())
            ->method('getEventDispatcher')
            ->willReturn($this->eventDispatcher);

        $this->indexerRegistryMock = $this->getMockBuilder('Magento\Framework\Indexer\IndexerRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->attribute = new Attribute(
            $this->contextMock,
            $this->registryMock,
            $this->extensionAttributesFactory,
            $this->attributeValueFactoryMock,
            $this->configMock,
            $this->typeFactoryMock,
            $this->storeManagerMock,
            $this->helperMock,
            $this->universalFactoryMock,
            $this->attributeOptionFactoryMock,
            $this->dataObjectProcessorMock,
            $this->dataObjectHelperMock,
            $this->timezoneMock,
            $this->reservedAttributeListMock,
            $this->resolverMock,
            $this->dateTimeFormatter,
            $this->indexerRegistryMock,
            $this->resourceMock
        );
    }

    /**
     * Test method
     */
    public function testAfterSaveEavCache()
    {
        $this->configMock
            ->expects($this->once())
            ->method('clear');

        $this->attribute->afterSave();
    }

    /**
     * Test method
     */
    public function testAfterDeleteEavCache()
    {
        $this->configMock
            ->expects($this->once())
            ->method('clear');

        $this->attribute->afterDelete();
    }

    public function testInvalidate()
    {
        /** @var IndexerInterface|\PHPUnit_Framework_MockObject_MockObject $indexerMock */
        $indexerMock = $this->getMockBuilder('Magento\Framework\Indexer\IndexerInterface')
            ->getMockForAbstractClass();

        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(Customer::CUSTOMER_GRID_INDEXER_ID)
            ->willReturn($indexerMock);

        $indexerMock->expects($this->once())
            ->method('invalidate');

        $this->attribute->invalidate();
    }

    /**
     * @param int $isSearchableInGrid
     * @param string $frontendInput
     * @param bool $result
     * @dataProvider dataProviderCanBeSearchableInGrid
     */
    public function testCanBeSearchableInGrid($isSearchableInGrid, $frontendInput, $result)
    {
        $this->attribute->setData('is_searchable_in_grid', $isSearchableInGrid);
        $this->attribute->setData(AttributeInterface::FRONTEND_INPUT, $frontendInput);

        $this->assertEquals($result, $this->attribute->canBeSearchableInGrid());
    }

    /**
     * @return array
     */
    public function dataProviderCanBeSearchableInGrid()
    {
        return [
            [0, 'text', false],
            [0, 'textarea', false],
            [1, 'text', true],
            [1, 'textarea', true],
            [1, 'date', false],
            [1, 'boolean', false],
            [1, 'select', false],
            [1, 'media_image', false],
            [1, 'gallery', false],
            [1, 'multiselect', false],
            [1, 'image', false],
            [1, 'price', false],
            [1, 'weight', false],
        ];
    }

    /**
     * @param int $isFilterableInGrid
     * @param string $frontendInput
     * @param bool $result
     * @dataProvider dataProviderCanBeFilterableInGrid
     */
    public function testCanBeFilterableInGrid($isFilterableInGrid, $frontendInput, $result)
    {
        $this->attribute->setData('is_filterable_in_grid', $isFilterableInGrid);
        $this->attribute->setData(AttributeInterface::FRONTEND_INPUT, $frontendInput);

        $this->assertEquals($result, $this->attribute->canBeFilterableInGrid());
    }

    /**
     * @return array
     */
    public function dataProviderCanBeFilterableInGrid()
    {
        return [
            [0, 'text', false],
            [0, 'date', false],
            [0, 'select', false],
            [0, 'boolean', false],
            [1, 'text', true],
            [1, 'date', true],
            [1, 'select', true],
            [1, 'boolean', true],
            [1, 'textarea', false],
            [1, 'media_image', false],
            [1, 'gallery', false],
            [1, 'multiselect', false],
            [1, 'image', false],
            [1, 'price', false],
            [1, 'weight', false],
        ];
    }
}
