<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cms\Test\Unit\Model;

use Magento\Cms\Model\PageRepository;
use Magento\Framework\Api\SortOrder;

/**
 * Test for Magento\Cms\Model\PageRepository
 */
class PageRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PageRepository
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Cms\Model\ResourceModel\Page
     */
    protected $pageResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Cms\Model\Page
     */
    protected $page;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Cms\Api\Data\PageInterface
     */
    protected $pageData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Cms\Api\Data\PageSearchResultsInterface
     */
    protected $pageSearchResult;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\DataObjectHelper
     */
    protected $dataHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Cms\Model\ResourceModel\Page\Collection
     */
    protected $collection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Initialize repository
     */
    public function setUp()
    {
        $this->pageResource = $this->getMockBuilder('Magento\Cms\Model\ResourceModel\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectProcessor = $this->getMockBuilder('Magento\Framework\Reflection\DataObjectProcessor')
            ->disableOriginalConstructor()
            ->getMock();
        $pageFactory = $this->getMockBuilder('Magento\Cms\Model\PageFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $pageDataFactory = $this->getMockBuilder('Magento\Cms\Api\Data\PageInterfaceFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $pageSearchResultFactory = $this->getMockBuilder('Magento\Cms\Api\Data\PageSearchResultsInterfaceFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $collectionFactory = $this->getMockBuilder('Magento\Cms\Model\ResourceModel\Page\CollectionFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $store = $this->getMockBuilder('\Magento\Store\Api\Data\StoreInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $store->expects($this->any())->method('getId')->willReturn(0);
        $this->storeManager->expects($this->any())->method('getStore')->willReturn($store);

        $this->page = $this->getMockBuilder('Magento\Cms\Model\Page')->disableOriginalConstructor()->getMock();
        $this->pageData = $this->getMockBuilder('Magento\Cms\Api\Data\PageInterface')
            ->getMock();
        $this->pageSearchResult = $this->getMockBuilder('Magento\Cms\Api\Data\PageSearchResultsInterface')
            ->getMock();
        $this->collection = $this->getMockBuilder('Magento\Cms\Model\ResourceModel\Page\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['addFieldToFilter', 'getSize', 'setCurPage', 'setPageSize', 'load', 'addOrder'])
            ->getMock();

        $pageFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->page);
        $pageDataFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->pageData);
        $pageSearchResultFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->pageSearchResult);
        $collectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->collection);
        /**
         * @var \Magento\Cms\Model\PageFactory $pageFactory
         * @var \Magento\Cms\Api\Data\PageInterfaceFactory $pageDataFactory
         * @var \Magento\Cms\Api\Data\PageSearchResultsInterfaceFactory $pageSearchResultFactory
         * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory
         */

        $this->dataHelper = $this->getMockBuilder('Magento\Framework\Api\DataObjectHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = new PageRepository(
            $this->pageResource,
            $pageFactory,
            $pageDataFactory,
            $collectionFactory,
            $pageSearchResultFactory,
            $this->dataHelper,
            $this->dataObjectProcessor,
            $this->storeManager
        );
    }

    /**
     * @test
     */
    public function testSave()
    {
        $this->pageResource->expects($this->once())
            ->method('save')
            ->with($this->page)
            ->willReturnSelf();
        $this->assertEquals($this->page, $this->repository->save($this->page));
    }

    /**
     * @test
     */
    public function testDeleteById()
    {
        $pageId = '123';

        $this->page->expects($this->once())
            ->method('getId')
            ->willReturn(true);
        $this->page->expects($this->once())
            ->method('load')
            ->with($pageId)
            ->willReturnSelf();
        $this->pageResource->expects($this->once())
            ->method('delete')
            ->with($this->page)
            ->willReturnSelf();

        $this->assertTrue($this->repository->deleteById($pageId));
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveException()
    {
        $this->pageResource->expects($this->once())
            ->method('save')
            ->with($this->page)
            ->willThrowException(new \Exception());
        $this->repository->save($this->page);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $this->pageResource->expects($this->once())
            ->method('delete')
            ->with($this->page)
            ->willThrowException(new \Exception());
        $this->repository->delete($this->page);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdException()
    {
        $pageId = '123';

        $this->page->expects($this->once())
            ->method('getId')
            ->willReturn(false);
        $this->page->expects($this->once())
            ->method('load')
            ->with($pageId)
            ->willReturnSelf();
        $this->repository->getById($pageId);
    }

    /**
     * @test
     */
    public function testGetList()
    {
        $field = 'name';
        $value = 'magento';
        $condition = 'eq';
        $total = 10;
        $currentPage = 3;
        $pageSize = 2;
        $sortField = 'id';

        $criteria = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaInterface')->getMock();
        $filterGroup = $this->getMockBuilder('Magento\Framework\Api\Search\FilterGroup')->getMock();
        $filter = $this->getMockBuilder('Magento\Framework\Api\Filter')->getMock();
        $storeFilter = $this->getMockBuilder('Magento\Framework\Api\Filter')->getMock();
        $sortOrder = $this->getMockBuilder('Magento\Framework\Api\SortOrder')->getMock();

        $criteria->expects($this->once())->method('getFilterGroups')->willReturn([$filterGroup]);
        $criteria->expects($this->once())->method('getSortOrders')->willReturn([$sortOrder]);
        $criteria->expects($this->once())->method('getCurrentPage')->willReturn($currentPage);
        $criteria->expects($this->once())->method('getPageSize')->willReturn($pageSize);
        $filterGroup->expects($this->once())->method('getFilters')->willReturn([$storeFilter, $filter]);
        $filter->expects($this->once())->method('getConditionType')->willReturn($condition);
        $filter->expects($this->any())->method('getField')->willReturn($field);
        $filter->expects($this->once())->method('getValue')->willReturn($value);
        $storeFilter->expects($this->any())->method('getField')->willReturn('store_id');
        $storeFilter->expects($this->once())->method('getValue')->willReturn(1);
        $sortOrder->expects($this->once())->method('getField')->willReturn($sortField);
        $sortOrder->expects($this->once())->method('getDirection')->willReturn(SortOrder::SORT_DESC);

        /** @var \Magento\Framework\Api\SearchCriteriaInterface $criteria */

        $this->collection->addItem($this->page);
        $this->pageSearchResult->expects($this->once())->method('setSearchCriteria')->with($criteria)->willReturnSelf();
        $this->collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with($field, [$condition => $value])
            ->willReturnSelf();
        $this->pageSearchResult->expects($this->once())->method('setTotalCount')->with($total)->willReturnSelf();
        $this->collection->expects($this->once())->method('getSize')->willReturn($total);
        $this->collection->expects($this->once())->method('setCurPage')->with($currentPage)->willReturnSelf();
        $this->collection->expects($this->once())->method('setPageSize')->with($pageSize)->willReturnSelf();
        $this->collection->expects($this->once())->method('addOrder')->with($sortField, 'DESC')->willReturnSelf();
        $this->page->expects($this->once())->method('getData')->willReturn(['data']);
        $this->pageSearchResult->expects($this->once())->method('setItems')->with(['someData'])->willReturnSelf();
        $this->dataHelper->expects($this->once())
            ->method('populateWithArray')
            ->with($this->pageData, ['data'], 'Magento\Cms\Api\Data\PageInterface');
        $this->dataObjectProcessor->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($this->pageData, 'Magento\Cms\Api\Data\PageInterface')
            ->willReturn('someData');

        $this->assertEquals($this->pageSearchResult, $this->repository->getList($criteria));
    }
}
