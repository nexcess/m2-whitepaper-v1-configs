<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\Helper;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Data\Customer as CustomerData;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Webapi\Rest\Request as RestRequest;

class Customer extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/customers';
    const SERVICE_NAME = 'customerAccountManagementV1';
    const SERVICE_VERSION = 'V1';

    const CONFIRMATION = 'a4fg7h893e39d';
    const CREATED_AT = '2013-11-05';
    const CREATED_IN = 'default';
    const STORE_NAME = 'Store Name';
    const DOB = '1970-01-01';
    const GENDER = 'Male';
    const GROUP_ID = 1;
    const MIDDLENAME = 'A';
    const PREFIX = 'Mr.';
    const STORE_ID = 1;
    const SUFFIX = 'Esq.';
    const TAXVAT = '12';
    const WEBSITE_ID = 1;

    /** Sample values for testing */
    const FIRSTNAME = 'Jane';
    const LASTNAME = 'Doe';
    const PASSWORD = 'test@123';

    const ADDRESS_CITY1 = 'CityM';
    const ADDRESS_CITY2 = 'CityX';
    const ADDRESS_REGION_CODE1 = 'AL';
    const ADDRESS_REGION_CODE2 = 'AL';

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    private $customerAddressFactory;
    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    private $customerDataFactory;
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /** @var DataObjectProcessor */
    private $dataObjectProcessor;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->customerAddressFactory = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Api\Data\AddressInterfaceFactory'
        );

        $this->customerDataFactory = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Api\Data\CustomerInterfaceFactory'
        );

        $this->dataObjectHelper = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\DataObjectHelper'
        );

        $this->dataObjectProcessor = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Reflection\DataObjectProcessor'
        );
    }

    public function createSampleCustomer()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestRequest::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateAccount',
            ],
        ];
        $customerDataArray = $this->dataObjectProcessor->buildOutputDataArray(
            $this->createSampleCustomerDataObject(),
            '\Magento\Customer\Api\Data\CustomerInterface'
        );
        $requestData = ['customer' => $customerDataArray, 'password' => self::PASSWORD];
        $customerData = $this->_webApiCall($serviceInfo, $requestData);
        return $customerData;
    }

    /**
     * Create customer using setters.
     *
     * @return CustomerInterface
     */
    public function createSampleCustomerDataObject()
    {
        $customerAddress1 = $this->customerAddressFactory->create();
        $customerAddress1->setCountryId('US');
        $customerAddress1->setIsDefaultBilling(true);
        $customerAddress1->setIsDefaultShipping(true);
        $customerAddress1->setPostcode('75477');
        $customerAddress1->setRegion(
            Bootstrap::getObjectManager()->create('Magento\Customer\Api\Data\RegionInterfaceFactory')
                ->create()
                ->setRegionCode(self::ADDRESS_REGION_CODE1)
                ->setRegion('Alabama')
                ->setRegionId(1)
        );
        $customerAddress1->setStreet(['Green str, 67']);
        $customerAddress1->setTelephone('3468676');
        $customerAddress1->setCity(self::ADDRESS_CITY1);
        $customerAddress1->setFirstname('John');
        $customerAddress1->setLastname('Smith');
        $address1 = $this->dataObjectProcessor->buildOutputDataArray(
            $customerAddress1,
            'Magento\Customer\Api\Data\AddressInterface'
        );

        $customerAddress2 = $this->customerAddressFactory->create();
        $customerAddress2->setCountryId('US');
        $customerAddress2->setIsDefaultBilling(false);
        $customerAddress2->setIsDefaultShipping(false);
        $customerAddress2->setPostcode('47676');
        $customerAddress2->setRegion(
            Bootstrap::getObjectManager()->create('Magento\Customer\Api\Data\RegionInterfaceFactory')
                ->create()
                ->setRegionCode(self::ADDRESS_REGION_CODE2)
                ->setRegion('Alabama')
                ->setRegionId(1)
        );
        $customerAddress2->setStreet(['Black str, 48', 'Building D']);
        $customerAddress2->setTelephone('3234676');
        $customerAddress2->setCity(self::ADDRESS_CITY2);
        $customerAddress2->setFirstname('John');
        $customerAddress2->setLastname('Smith');
        $address2 = $this->dataObjectProcessor->buildOutputDataArray(
            $customerAddress2,
            'Magento\Customer\Api\Data\AddressInterface'
        );

        $customerData = [
            CustomerData::FIRSTNAME => self::FIRSTNAME,
            CustomerData::LASTNAME => self::LASTNAME,
            CustomerData::EMAIL => 'janedoe' . uniqid() . '@example.com',
            CustomerData::CONFIRMATION => self::CONFIRMATION,
            CustomerData::CREATED_AT => self::CREATED_AT,
            CustomerData::CREATED_IN => self::STORE_NAME,
            CustomerData::DOB => self::DOB,
            CustomerData::GENDER => self::GENDER,
            CustomerData::GROUP_ID => self::GROUP_ID,
            CustomerData::MIDDLENAME => self::MIDDLENAME,
            CustomerData::PREFIX => self::PREFIX,
            CustomerData::STORE_ID => self::STORE_ID,
            CustomerData::SUFFIX => self::SUFFIX,
            CustomerData::TAXVAT => self::TAXVAT,
            CustomerData::WEBSITE_ID => self::WEBSITE_ID,
            CustomerData::KEY_ADDRESSES => [$address1, $address2],
            'custom_attributes' => [
                [
                    'attribute_code' => 'disable_auto_group_change',
                    'value' => '0',
                ],
            ],
        ];
        $customer = $this->customerDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customer,
            $customerData,
            '\Magento\Customer\Api\Data\CustomerInterface'
        );
        return $customer;
    }
}
