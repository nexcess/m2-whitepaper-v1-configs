<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Unit\Model\ResourceModel;

use Magento\Customer\Api\CustomerMetadataInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Data\CustomerSecureFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSecureFactory;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Customer\Model\ResourceModel\AddressRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerResourceModel;

    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMetadata;

    /**
     * @var \Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Framework\Api\ImageProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $imageProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customer;

    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    protected $model;
    
    public function setUp()
    {
        $this->customerResourceModel =
            $this->getMock('Magento\Customer\Model\ResourceModel\Customer', [], [], '', false);
        $this->customerRegistry = $this->getMock('Magento\Customer\Model\CustomerRegistry', [], [], '', false);
        $this->dataObjectHelper = $this->getMock('Magento\Framework\Api\DataObjectHelper', [], [], '', false);
        $this->customerFactory  = $this->getMock('Magento\Customer\Model\CustomerFactory', ['create'], [], '', false);
        $this->customerSecureFactory  = $this->getMock(
            'Magento\Customer\Model\Data\CustomerSecureFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->addressRepository = $this->getMock(
            'Magento\Customer\Model\ResourceModel\AddressRepository',
            [],
            [],
            '',
            false
        );

        $this->customerMetadata = $this->getMockForAbstractClass(
            'Magento\Customer\Api\CustomerMetadataInterface',
            [],
            '',
            false
        );
        $this->searchResultsFactory = $this->getMock(
            'Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->eventManager = $this->getMockForAbstractClass(
            'Magento\Framework\Event\ManagerInterface',
            [],
            '',
            false
        );
        $this->storeManager = $this->getMockForAbstractClass(
            'Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            false
        );
        $this->extensibleDataObjectConverter = $this->getMock(
            'Magento\Framework\Api\ExtensibleDataObjectConverter',
            [],
            [],
            '',
            false
        );
        $this->imageProcessor = $this->getMockForAbstractClass(
            'Magento\Framework\Api\ImageProcessorInterface',
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
        $this->customer = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\CustomerInterface',
            [],
            '',
            false
        );

        $this->model = new \Magento\Customer\Model\ResourceModel\CustomerRepository(
            $this->customerFactory,
            $this->customerSecureFactory,
            $this->customerRegistry,
            $this->addressRepository,
            $this->customerResourceModel,
            $this->customerMetadata,
            $this->searchResultsFactory,
            $this->eventManager,
            $this->storeManager,
            $this->extensibleDataObjectConverter,
            $this->dataObjectHelper,
            $this->imageProcessor,
            $this->extensionAttributesJoinProcessor
        );
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function prepareMocksForValidation($isValid = false)
    {
        $attributeMetaData = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AttributeMetadataInterface',
            [],
            '',
            false
        );
        $attributeMetaData->expects($this->atLeastOnce())
            ->method('isRequired')
            ->willReturn(true);
        $this->customerMetadata->expects($this->atLeastOnce())
            ->method('getAttributeMetadata')
            ->willReturn($attributeMetaData);

        $this->customer->expects($this->once())
            ->method('getFirstname')
            ->willReturn($isValid ? 'Firstname' : false);
        $this->customer->expects($this->once())
            ->method('getLastname')
            ->willReturn($isValid ? 'Lastname' : false);
        $this->customer->expects($this->atLeastOnce())
            ->method('getEmail')
            ->willReturn($isValid ? 'example@example.com' : false);
        $this->customer->expects($this->once())
            ->method('getDob')
            ->willReturn($isValid ? '12/12/2015' : false);
        $this->customer->expects($this->atLeastOnce())
            ->method('getTaxvat')
            ->willReturn($isValid ? 'taxvat' : false);
        $this->customer->expects($this->once())
            ->method('getGender')
            ->willReturn($isValid ? 'gender' : false);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSave()
    {
        $customerId = 1;
        $storeId = 2;
        $this->prepareMocksForValidation(true);

        $region = $this->getMockForAbstractClass('Magento\Customer\Api\Data\RegionInterface', [], '', false);
        $address = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressInterface',
            [],
            '',
            false,
            false,
            true,
            [
                'setCustomerId',
                'setRegion',
                'getRegion',
                'getId'
            ]
        );
        $address2 = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressInterface',
            [],
            '',
            false,
            false,
            true,
            [
                'setCustomerId',
                'setRegion',
                'getRegion',
                'getId'
            ]
        );
        $customerModel = $this->getMock(
            'Magento\Customer\Model\Customer',
            [
                'getId',
                'setId',
                'setStoreId',
                'getStoreId',
                'getAttributeSetId',
                'setAttributeSetId',
                'setRpToken',
                'setRpTokenCreatedAt',
                'getDataModel',
                'setPasswordHash',
                'save',
            ],
            [],
            '',
            false
        );
        $customerAttributesMetaData = $this->getMockForAbstractClass(
            'Magento\Framework\Api\CustomAttributesDataInterface',
            [],
            '',
            false,
            false,
            true,
            [
                'getId',
                'getEmail',
                'getWebsiteId',
                'getAddresses',
                'setAddresses'
            ]
        );
        $customerSecureData = $this->getMock(
            'Magento\Customer\Model\Data\CustomerSecure',
            [
                'getRpToken',
                'getRpTokenCreatedAt',
                'getPasswordHash'
            ],
            [],
            '',
            false
        );
        $this->customer->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($customerId);
        $this->customerRegistry->expects($this->atLeastOnce())
            ->method('retrieve')
            ->with($customerId)
            ->willReturn($customerModel);
        $customerModel->expects($this->atLeastOnce())
            ->method('getDataModel')
            ->willReturn($this->customer);
        $this->imageProcessor->expects($this->once())
            ->method('save')
            ->with($this->customer, CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $this->customer)
            ->willReturn($customerAttributesMetaData);
        $address->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $address->expects($this->once())
            ->method('getRegion')
            ->willReturn($region);
        $address->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(7);
        $address->expects($this->once())
            ->method('setRegion')
            ->with($region);
        $customerAttributesMetaData->expects($this->atLeastOnce())
            ->method('getAddresses')
            ->willReturn([$address]);
        $customerAttributesMetaData->expects($this->at(1))
            ->method('setAddresses')
            ->with([]);
        $customerAttributesMetaData->expects($this->at(2))
            ->method('setAddresses')
            ->with([$address]);
        $this->extensibleDataObjectConverter->expects($this->once())
            ->method('toNestedArray')
            ->with($customerAttributesMetaData, [], '\Magento\Customer\Api\Data\CustomerInterface')
            ->willReturn(['customerData']);
        $this->customerFactory->expects($this->once())
            ->method('create')
            ->with(['data' => ['customerData']])
            ->willReturn($customerModel);
        $customerModel->expects($this->once())
            ->method('getStoreId')
            ->willReturn(null);
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManager
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($store);
        $customerModel->expects($this->once())
            ->method('setStoreId')
            ->with($storeId);
        $customerModel->expects($this->once())
            ->method('setId')
            ->with($customerId);
        $customerModel->expects($this->once())
            ->method('getAttributeSetId')
            ->willReturn(null);
        $customerModel->expects($this->once())
            ->method('setAttributeSetId')
            ->with(\Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER);
        $customerAttributesMetaData->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($customerId);
        $this->customerRegistry->expects($this->once())
            ->method('retrieveSecureData')
            ->with($customerId)
            ->willReturn($customerSecureData);
        $customerSecureData->expects($this->once())
            ->method('getRpToken')
            ->willReturn('rpToken');
        $customerSecureData->expects($this->once())
            ->method('getRpTokenCreatedAt')
            ->willReturn('rpTokenCreatedAt');
        $customerSecureData->expects($this->once())
            ->method('getPasswordHash')
            ->willReturn('passwordHash');

        $customerModel->expects($this->exactly(2))
            ->method('setRpToken')
            ->willReturnMap([
                ['rpToken', $customerModel],
                [null, $customerModel],
            ]);
        $customerModel->expects($this->exactly(2))
            ->method('setRpTokenCreatedAt')
            ->willReturnMap([
                ['rpTokenCreatedAt', $customerModel],
                [null, $customerModel],
            ]);

        $customerModel->expects($this->once())
            ->method('setPasswordHash')
            ->with('passwordHash');
        $customerModel->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($customerId);
        $customerModel->expects($this->once())
            ->method('save');
        $this->customerRegistry->expects($this->once())
            ->method('push')
            ->with($customerModel);
        $this->customer->expects($this->once())
            ->method('getAddresses')
            ->willReturn([$address, $address2]);
        $this->addressRepository->expects($this->once())
            ->method('save')
            ->with($address);
        $customerAttributesMetaData->expects($this->once())
            ->method('getEmail')
            ->willReturn('example@example.com');
        $customerAttributesMetaData->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(2);
        $this->customerRegistry->expects($this->once())
            ->method('retrieveByEmail')
            ->with('example@example.com', 2)
            ->willReturn($customerModel);
        $this->eventManager->expects($this->once())
            ->method('dispatch')
            ->with(
                'customer_save_after_data_object',
                ['customer_data_object' => $this->customer, 'orig_customer_data_object' => $customerAttributesMetaData]
            );

        $this->model->save($this->customer);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSaveWithPasswordHash()
    {
        $customerId = 1;
        $storeId = 2;
        $passwordHash = 'ukfa4sdfa56s5df02asdf4rt';
        $this->prepareMocksForValidation(true);

        $region = $this->getMockForAbstractClass('Magento\Customer\Api\Data\RegionInterface', [], '', false);
        $address = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressInterface',
            [],
            '',
            false,
            false,
            true,
            [
                'setCustomerId',
                'setRegion',
                'getRegion',
                'getId'
            ]
        );
        $address2 = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressInterface',
            [],
            '',
            false,
            false,
            true,
            [
                'setCustomerId',
                'setRegion',
                'getRegion',
                'getId'
            ]
        );
        $customerModel = $this->getMock(
            'Magento\Customer\Model\Customer',
            [
                'getId',
                'setId',
                'setStoreId',
                'getStoreId',
                'getAttributeSetId',
                'setAttributeSetId',
                'setRpToken',
                'setRpTokenCreatedAt',
                'getDataModel',
                'setPasswordHash',
                'save',
            ],
            [],
            '',
            false
        );
        $customerAttributesMetaData = $this->getMockForAbstractClass(
            'Magento\Framework\Api\CustomAttributesDataInterface',
            [],
            '',
            false,
            false,
            true,
            [
                'getId',
                'getEmail',
                'getWebsiteId',
                'getAddresses',
                'setAddresses'
            ]
        );
        $this->customer->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($customerId);
        $this->customerRegistry->expects($this->atLeastOnce())
            ->method('retrieve')
            ->with($customerId)
            ->willReturn($customerModel);
        $customerModel->expects($this->atLeastOnce())
            ->method('getDataModel')
            ->willReturn($this->customer);
        $this->imageProcessor->expects($this->once())
            ->method('save')
            ->with($this->customer, CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $this->customer)
            ->willReturn($customerAttributesMetaData);
        $address->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $address->expects($this->once())
            ->method('getRegion')
            ->willReturn($region);
        $address->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(7);
        $address->expects($this->once())
            ->method('setRegion')
            ->with($region);
        $customerAttributesMetaData->expects($this->any())
            ->method('getAddresses')
            ->willReturn([$address]);
        $customerAttributesMetaData->expects($this->at(1))
            ->method('setAddresses')
            ->with([]);
        $customerAttributesMetaData->expects($this->at(2))
            ->method('setAddresses')
            ->with([$address]);
        $this->extensibleDataObjectConverter->expects($this->once())
            ->method('toNestedArray')
            ->with($customerAttributesMetaData, [], '\Magento\Customer\Api\Data\CustomerInterface')
            ->willReturn(['customerData']);
        $this->customerFactory->expects($this->once())
            ->method('create')
            ->with(['data' => ['customerData']])
            ->willReturn($customerModel);
        $customerModel->expects($this->once())
            ->method('getStoreId')
            ->willReturn(null);
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManager
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($store);
        $customerModel->expects($this->once())
            ->method('setStoreId')
            ->with($storeId);
        $customerModel->expects($this->once())
            ->method('setId')
            ->with(null);
        $customerModel->expects($this->once())
            ->method('getAttributeSetId')
            ->willReturn(null);
        $customerModel->expects($this->once())
            ->method('setAttributeSetId')
            ->with(\Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER);
        $customerModel->expects($this->once())
            ->method('setPasswordHash')
            ->with($passwordHash);
        $customerModel->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($customerId);
        $customerModel->expects($this->once())
            ->method('save');
        $this->customerRegistry->expects($this->once())
            ->method('push')
            ->with($customerModel);
        $this->customer->expects($this->any())
            ->method('getAddresses')
            ->willReturn([$address, $address2]);
        $this->addressRepository->expects($this->once())
            ->method('save')
            ->with($address);
        $customerAttributesMetaData->expects($this->once())
            ->method('getEmail')
            ->willReturn('example@example.com');
        $customerAttributesMetaData->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(2);
        $this->customerRegistry->expects($this->once())
            ->method('retrieveByEmail')
            ->with('example@example.com', 2)
            ->willReturn($customerModel);
        $this->eventManager->expects($this->once())
            ->method('dispatch')
            ->with(
                'customer_save_after_data_object',
                ['customer_data_object' => $this->customer, 'orig_customer_data_object' => $customerAttributesMetaData]
            );

        $this->model->save($this->customer, $passwordHash);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testSaveWithException()
    {
        $this->prepareMocksForValidation(false);
        $this->model->save($this->customer);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $sortOrder = $this->getMock('Magento\Framework\Api\SortOrder', [], [], '', false);
        $filterGroup = $this->getMock('Magento\Framework\Api\Search\FilterGroup', [], [], '', false);
        $filter = $this->getMock('Magento\Framework\Api\Filter', [], [], '', false);
        $collection = $this->getMock('Magento\Customer\Model\ResourceModel\Customer\Collection', [], [], '', false);
        $searchResults = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressSearchResultsInterface',
            [],
            '',
            false
        );
        $searchCriteria = $this->getMockForAbstractClass(
            'Magento\Framework\Api\SearchCriteriaInterface',
            [],
            '',
            false
        );
        $customerModel = $this->getMock(
            'Magento\Customer\Model\Customer',
            [
                'getId',
                'setId',
                'setStoreId',
                'getStoreId',
                'getAttributeSetId',
                'setAttributeSetId',
                'setRpToken',
                'setRpTokenCreatedAt',
                'getDataModel',
                'setPasswordHash',
                'getCollection'
            ],
            [],
            'customerModel',
            false
        );
        $metadata = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AttributeMetadataInterface',
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
        $this->customerFactory->expects($this->once())
            ->method('create')
            ->willReturn($customerModel);
        $customerModel->expects($this->once())
            ->method('getCollection')
            ->willReturn($collection);
        $this->extensionAttributesJoinProcessor->expects($this->once())
            ->method('process')
            ->with($collection, 'Magento\Customer\Api\Data\CustomerInterface');
        $this->customerMetadata->expects($this->once())
            ->method('getAllAttributesMetadata')
            ->willReturn([$metadata]);
        $metadata->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn('attribute-code');
        $collection->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('attribute-code');
        $collection->expects($this->once())
            ->method('addNameToSelect');
        $collection->expects($this->at(2))
            ->method('joinAttribute')
            ->with('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->willReturnSelf();
        $collection->expects($this->at(3))
            ->method('joinAttribute')
            ->with('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->willReturnSelf();
        $collection->expects($this->at(4))
            ->method('joinAttribute')
            ->with('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->willReturnSelf();
        $collection->expects($this->at(5))
            ->method('joinAttribute')
            ->with('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->willReturnSelf();
        $collection->expects($this->at(6))
            ->method('joinAttribute')
            ->with('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->willReturnSelf();
        $collection->expects($this->at(7))
            ->method('joinAttribute')
            ->with('company', 'customer_address/company', 'default_billing', null, 'left')
            ->willReturnSelf();
        $searchCriteria->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroup]);
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with([['attribute' => 'Field', 'eq' => 'Value']], []);
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
            ->method('addOrder')
            ->with('Field', 'ASC');
        $searchCriteria->expects($this->atLeastOnce())
            ->method('getSortOrders')
            ->willReturn([$sortOrder]);
        $sortOrder->expects($this->once())
            ->method('getField')
            ->willReturn('Field');
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
            ->method('getSize')
            ->willReturn(23);
        $searchResults->expects($this->once())
            ->method('setTotalCount')
            ->with(23);
        $collection->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$customerModel]));
        $customerModel->expects($this->atLeastOnce())
            ->method('getDataModel')
            ->willReturn($this->customer);
        $searchResults->expects($this->once())
            ->method('setItems')
            ->with([$this->customer]);

        $this->assertSame($searchResults, $this->model->getList($searchCriteria));
    }

    public function testDeleteById()
    {
        $customerId = 14;
        $customerModel = $this->getMock(
            'Magento\Customer\Model\Customer',
            ['delete'],
            [],
            '',
            false
        );
        $this->customerRegistry
            ->expects($this->once())
            ->method('retrieve')
            ->with($customerId)
            ->willReturn($customerModel);
        $customerModel->expects($this->once())
            ->method('delete');
        $this->customerRegistry->expects($this->once())
            ->method('remove')
            ->with($customerId);

        $this->assertTrue($this->model->deleteById($customerId));
    }


    public function testDelete()
    {
        $customerId = 14;
        $customerModel = $this->getMock(
            'Magento\Customer\Model\Customer',
            ['delete'],
            [],
            '',
            false
        );

        $this->customer->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        $this->customerRegistry
            ->expects($this->once())
            ->method('retrieve')
            ->with($customerId)
            ->willReturn($customerModel);
        $customerModel->expects($this->once())
            ->method('delete');
        $this->customerRegistry->expects($this->once())
            ->method('remove')
            ->with($customerId);

        $this->assertTrue($this->model->delete($this->customer));
    }
}
