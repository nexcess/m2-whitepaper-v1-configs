<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Unit\Model\ResourceModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GroupRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\GroupRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupRegistry;

    /**
     * @var \Magento\Customer\Model\GroupFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupFactory;

    /**
     * @var \Magento\Customer\Model\Group|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupModel;

    /**
     * @var \Magento\Customer\Api\Data\GroupInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupDataFactory;

    /**
     * @var \Magento\Customer\Api\Data\GroupInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $group;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupResourceModel;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Customer\Api\Data\GroupSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Customer\Api\Data\GroupSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResults;

    /**
     * @var \Magento\Tax\Api\TaxClassRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $taxClassRepository;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \Magento\Customer\Model\ResourceModel\GroupRepository
     */
    protected $model;
    
    public function setUp()
    {
        $this->groupRegistry = $this->getMock(
            'Magento\Customer\Model\GroupRegistry',
            [],
            [],
            '',
            false
        );
        $this->groupFactory = $this->getMock(
            'Magento\Customer\Model\GroupFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->groupModel = $this->getMock(
            'Magento\Customer\Model\Group',
            [
                'getTaxClassId',
                'getTaxClassName',
                'getId',
                'getCode',
                'setDataUsingMethod',
                'setCode',
                'setTaxClassId',
                'usesAsDefault',
                'delete',
                'getCollection',
                'getData',
            ],
            [],
            'groupModel',
            false
        );
        $this->groupDataFactory = $this->getMock(
            'Magento\Customer\Api\Data\GroupInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->group = $this->getMockForAbstractClass('Magento\Customer\Api\Data\GroupInterface', [], 'group', false);

        $this->groupResourceModel = $this->getMock(
            'Magento\Customer\Model\ResourceModel\Group',
            [],
            [],
            '',
            false
        );
        $this->dataObjectProcessor = $this->getMock(
            'Magento\Framework\Reflection\DataObjectProcessor',
            [],
            [],
            '',
            false
        );
        $this->searchResultsFactory = $this->getMock(
            'Magento\Customer\Api\Data\GroupSearchResultsInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->searchResults = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\GroupSearchResultsInterface',
            [],
            '',
            false
        );
        $this->taxClassRepository = $this->getMockForAbstractClass(
            'Magento\Tax\Api\TaxClassRepositoryInterface',
            [],
            '',
            false
        );
        $this->extensionAttributesJoinProcessor = $this->getMockForAbstractClass(
            'Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface',
            [],
            '',
            false
        );
        $this->model = new \Magento\Customer\Model\ResourceModel\GroupRepository(
            $this->groupRegistry,
            $this->groupFactory,
            $this->groupDataFactory,
            $this->groupResourceModel,
            $this->dataObjectProcessor,
            $this->searchResultsFactory,
            $this->taxClassRepository,
            $this->extensionAttributesJoinProcessor
        );
    }

    public function testSave()
    {
        $groupId = 23;

        $taxClass = $this->getMockForAbstractClass('Magento\Tax\Api\Data\TaxClassInterface', [], '', false);

        $this->group->expects($this->once())
            ->method('getCode')
            ->willReturn('Code');
        $this->group->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($groupId);
        $this->group->expects($this->once())
            ->method('getTaxClassId')
            ->willReturn(17);

        $this->groupModel->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($groupId);
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('Code');
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getTaxClassId')
            ->willReturn(234);
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getTaxClassName')
            ->willReturn('Tax class name');
        $this->group->expects($this->once())
            ->method('setId')
            ->with($groupId)
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setCode')
            ->with('Code')
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setTaxClassId')
            ->with(234)
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setTaxClassName')
            ->with('Tax class name')
            ->willReturnSelf();

        $this->taxClassRepository->expects($this->once())
            ->method('get')
            ->with(17)
            ->willReturn($taxClass);
        $taxClass->expects($this->once())
            ->method('getClassType')
            ->willReturn('CUSTOMER');

        $this->groupRegistry->expects($this->once())
            ->method('retrieve')
            ->with($groupId)
            ->willReturn($this->groupModel);
        $this->dataObjectProcessor->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($this->group, '\Magento\Customer\Api\Data\GroupInterface')
            ->willReturn(['attributeCode' => 'attributeData']);
        $this->groupModel->expects($this->once())
            ->method('setDataUsingMethod')
            ->with('attributeCode', 'attributeData');
        $this->groupResourceModel->expects($this->once())
            ->method('save')
            ->with($this->groupModel);
        $this->groupRegistry->expects($this->once())
            ->method('remove')
            ->with($groupId);
        $this->groupDataFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->group);

        $this->assertSame($this->group, $this->model->save($this->group));
    }

    /**
     * @expectedException \Magento\Framework\Exception\State\InvalidTransitionException
     */
    public function testSaveWithException()
    {
        $taxClass = $this->getMockForAbstractClass('Magento\Tax\Api\Data\TaxClassInterface', [], '', false);

        $this->groupFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->groupModel);

        $this->group->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('Code');
        $this->group->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(false);
        $this->group->expects($this->atLeastOnce())
            ->method('getTaxClassId')
            ->willReturn(234);
        $this->group->expects($this->atLeastOnce())
            ->method('getTaxClassId')
            ->willReturn(17);

        $this->groupModel->expects($this->once())
            ->method('setCode')
            ->with('Code');
        $this->groupModel->expects($this->once())
            ->method('setTaxClassId')
            ->with(234);

        $this->taxClassRepository->expects($this->once())
            ->method('get')
            ->with(234)
            ->willReturn($taxClass);
        $taxClass->expects($this->once())
            ->method('getClassType')
            ->willReturn('CUSTOMER');

        $this->groupResourceModel->expects($this->once())
            ->method('save')
            ->with($this->groupModel)
            ->willThrowException(new \Magento\Framework\Exception\LocalizedException(
                new \Magento\Framework\Phrase('Customer Group already exists.')
            ));

        $this->model->save($this->group);
    }

    public function testGetById()
    {
        $groupId = 86;

        $this->groupRegistry->expects($this->once())
            ->method('retrieve')
            ->with($groupId)
            ->willReturn($this->groupModel);

        $this->groupDataFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->group);

        $this->group->expects($this->once())
            ->method('setId')
            ->with($groupId)
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setCode')
            ->with('Code')
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setTaxClassId')
            ->with(234)
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setTaxClassName')
            ->with('Tax class name')
            ->willReturnSelf();

        $this->groupModel->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($groupId);
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('Code');
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getTaxClassId')
            ->willReturn(234);
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getTaxClassName')
            ->willReturn('Tax class name');

        $this->assertSame($this->group, $this->model->getById($groupId));
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $groupId = 86;

        $groupExtension = $this->getMock('Magento\Customer\Api\Data\GroupExtensionInterface', [], [], '', false);
        $filterGroup = $this->getMock('Magento\Framework\Api\Search\FilterGroup', [], [], '', false);
        $filter = $this->getMock('Magento\Framework\Api\Filter', [], [], '', false);
        $collection = $this->getMock('Magento\Customer\Model\ResourceModel\Group\Collection', [], [], '', false);
        $sortOrder = $this->getMock('Magento\Framework\Api\SortOrder', [], [], '', false);
        $searchCriteria = $this->getMockForAbstractClass(
            'Magento\Framework\Api\SearchCriteriaInterface',
            [],
            '',
            false
        );
        $searchResults = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressSearchResultsInterface',
            [],
            '',
            false
        );
        $this->searchResultsFactory->expects($this->once())
            ->method('create')
            ->willReturn($searchResults);
        $searchResults->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteria);

        $this->groupFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->groupModel);
        $this->groupModel->expects($this->once())
            ->method('getCollection')
            ->willReturn($collection);
        $this->extensionAttributesJoinProcessor->expects($this->once())
            ->method('process')
            ->with($collection, 'Magento\Customer\Api\Data\GroupInterface');
        $collection->expects($this->once())
            ->method('addTaxClass');
        $searchCriteria->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroup]);
        $filterGroup->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filter]);
        $filter->expects($this->once())
            ->method('getConditionType')
            ->willReturn(false);
        $filter->expects($this->once())
            ->method('getField')
            ->willReturn('Field');
        $filter->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn('Value');
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with(['Field'], [['eq' => 'Value']]);
        $searchCriteria->expects($this->atLeastOnce())
            ->method('getSortOrders')
            ->willReturn([$sortOrder]);
        $sortOrder->expects($this->once())
            ->method('getField')
            ->willReturn('Field');
        $collection->expects($this->once())
            ->method('addOrder')
            ->with('Field', 'ASC');
        $sortOrder->expects($this->once())
            ->method('getDirection')
            ->willReturn(\Magento\Framework\Api\SortOrder::SORT_ASC);
        $searchCriteria->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn(1);
        $collection->expects($this->once())
            ->method('setCurPage')
            ->with(1);
        $searchCriteria->expects($this->once())
            ->method('getPageSize')
            ->willReturn(10);
        $collection->expects($this->once())
            ->method('setPageSize')
            ->with(10);
        $collection->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$this->groupModel]));

        $this->groupDataFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->group);

        $this->group->expects($this->once())
            ->method('setId')
            ->with($groupId)
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setCode')
            ->with('Code')
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setTaxClassId')
            ->with(234)
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setTaxClassName')
            ->with('Tax class name')
            ->willReturnSelf();

        $this->groupModel->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($groupId);
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('Code');
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getTaxClassId')
            ->willReturn(234);
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getTaxClassName')
            ->willReturn('Tax class name');
        $this->groupModel->expects($this->once())
            ->method('getData')
            ->willReturn([]);
        $this->extensionAttributesJoinProcessor->expects($this->once())
            ->method('extractExtensionAttributes')
            ->with('Magento\Customer\Api\Data\GroupInterface', [])
            ->willReturn(['extension_attributes' => $groupExtension]);
        $this->group->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($groupExtension);
        $collection
            ->expects($this->once())
            ->method('getSize')
            ->willReturn(9);
        $searchResults->expects($this->once())
            ->method('setTotalCount')
            ->with(9);
        $searchResults->expects($this->once())
            ->method('setItems')
            ->with([$this->group])
            ->willReturnSelf();

        $this->assertSame($searchResults, $this->model->getList($searchCriteria));
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetListWithoutSortOrder()
    {
        $groupId = 86;

        $groupExtension = $this->getMock('Magento\Customer\Api\Data\GroupExtensionInterface', [], [], '', false);
        $filterGroup = $this->getMock('Magento\Framework\Api\Search\FilterGroup', [], [], '', false);
        $filter = $this->getMock('Magento\Framework\Api\Filter', [], [], '', false);
        $collection = $this->getMock('Magento\Customer\Model\ResourceModel\Group\Collection', [], [], '', false);
        $searchCriteria = $this->getMockForAbstractClass(
            'Magento\Framework\Api\SearchCriteriaInterface',
            [],
            '',
            false
        );
        $searchResults = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressSearchResultsInterface',
            [],
            '',
            false
        );
        $this->searchResultsFactory->expects($this->once())
            ->method('create')
            ->willReturn($searchResults);
        $searchResults->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteria);

        $this->groupFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->groupModel);
        $this->groupModel->expects($this->once())
            ->method('getCollection')
            ->willReturn($collection);
        $this->extensionAttributesJoinProcessor->expects($this->once())
            ->method('process')
            ->with($collection, 'Magento\Customer\Api\Data\GroupInterface');
        $collection->expects($this->once())
            ->method('addTaxClass');
        $searchCriteria->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroup]);
        $filterGroup->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filter]);
        $filter->expects($this->once())
            ->method('getConditionType')
            ->willReturn(false);
        $filter->expects($this->once())
            ->method('getField')
            ->willReturn('Field');
        $filter->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn('Value');
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with(['Field'], [['eq' => 'Value']]);

        $searchCriteria->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn(1);
        $collection->expects($this->once())
            ->method('setCurPage')
            ->with(1);
        $searchCriteria->expects($this->once())
            ->method('getPageSize')
            ->willReturn(10);
        $collection->expects($this->once())
            ->method('setPageSize')
            ->with(10);
        $collection->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$this->groupModel]));

        $this->groupDataFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->group);

        $this->group->expects($this->once())
            ->method('setId')
            ->with($groupId)
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setCode')
            ->with('Code')
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setTaxClassId')
            ->with(234)
            ->willReturnSelf();
        $this->group->expects($this->once())
            ->method('setTaxClassName')
            ->with('Tax class name')
            ->willReturnSelf();

        $this->groupModel->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($groupId);
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('Code');
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getTaxClassId')
            ->willReturn(234);
        $this->groupModel->expects($this->atLeastOnce())
            ->method('getTaxClassName')
            ->willReturn('Tax class name');
        $this->groupModel->expects($this->once())
            ->method('getData')
            ->willReturn([]);
        $this->extensionAttributesJoinProcessor->expects($this->once())
            ->method('extractExtensionAttributes')
            ->with('Magento\Customer\Api\Data\GroupInterface', [])
            ->willReturn(['extension_attributes' => $groupExtension]);
        $this->group->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($groupExtension);
        $collection->expects($this->once())
            ->method('getSize')
            ->willReturn(9);
        $searchResults->expects($this->once())
            ->method('setTotalCount')
            ->with(9);
        $searchResults->expects($this->once())
            ->method('setItems')
            ->with([$this->group])
            ->willReturnSelf();
        $collection->expects($this->once())
            ->method('addOrder')
            ->with('customer_group_id', 'ASC');

        $this->assertSame($searchResults, $this->model->getList($searchCriteria));
    }

    public function testDeleteById()
    {
        $groupId = 6;
        $this->groupRegistry->expects($this->once())
            ->method('retrieve')
            ->with($groupId)
            ->willReturn($this->groupModel);
        $this->groupModel->expects($this->once())
            ->method('usesAsDefault')
            ->willReturn(false);
        $this->groupModel->expects($this->once())
            ->method('delete');
        $this->groupRegistry
            ->expects($this->once())
            ->method('remove')
            ->with($groupId);

        $this->assertTrue($this->model->deleteById($groupId));
    }

    public function testDelete()
    {
        $groupId = 6;
        $this->group->expects($this->once())
            ->method('getId')
            ->willReturn($groupId);
        $this->groupRegistry->expects($this->once())
            ->method('retrieve')
            ->with($groupId)
            ->willReturn($this->groupModel);
        $this->groupModel->expects($this->once())
            ->method('usesAsDefault')
            ->willReturn(false);
        $this->groupModel->expects($this->once())
            ->method('delete');
        $this->groupRegistry
            ->expects($this->once())
            ->method('remove')
            ->with($groupId);

        $this->assertTrue($this->model->delete($this->group));
    }
}
