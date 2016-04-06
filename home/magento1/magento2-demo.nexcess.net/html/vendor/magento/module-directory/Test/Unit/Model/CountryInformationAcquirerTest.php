<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Directory\Test\Unit\Model;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CountryInformationAcquirerTest
 */
class CountryInformationAcquirerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Directory\Model\CountryInformationAcquirer
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $countryInformationFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $regionInformationFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $className = '\Magento\Directory\Model\Data\CountryInformationFactory';
        $this->countryInformationFactory = $this->getMock($className, ['create'], [], '', false);

        $className = '\Magento\Directory\Model\Data\RegionInformationFactory';
        $this->regionInformationFactory = $this->getMock($className, ['create'], [], '', false);

        $className = '\Magento\Directory\Helper\Data';
        $this->directoryHelper = $this->getMock($className, ['getCountryCollection', 'getRegionData'], [], '', false);

        $className = '\Magento\Store\Model\StoreManager';
        $this->storeManager = $this->getMock($className, ['getStore'], [], '', false);

        $this->model = $this->objectManager->getObject(
            '\Magento\Directory\Model\CountryInformationAcquirer',
            [
                'countryInformationFactory' => $this->countryInformationFactory,
                'regionInformationFactory' => $this->regionInformationFactory,
                'directoryHelper' => $this->directoryHelper,
                'storeManager' => $this->storeManager,
            ]
        );
    }

    /**
     * test GetCountriesInfo
     */
    public function testGetCountriesInfo()
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManager->expects($this->once())->method('getStore')->willReturn($store);

        $testCountryInfo = $this->objectManager->getObject('\Magento\Directory\Model\Country');
        $testCountryInfo->setData('country_id', 'US');
        $testCountryInfo->setData('iso2_code', 'US');
        $testCountryInfo->setData('iso3_code', 'USA');
        $testCountryInfo->setData('name_default', 'United States of America');
        $testCountryInfo->setData('name_en_US', 'United States of America');
        $countries = ['US' => $testCountryInfo];
        $this->directoryHelper->expects($this->once())->method('getCountryCollection')->willReturn($countries);

        $regions = ['US' => ['TX' => ['code' => 'TX', 'name' => 'Texas']]];
        $this->directoryHelper->expects($this->once())->method('getRegionData')->willReturn($regions);

        $countryInfo = $this->objectManager->getObject('\Magento\Directory\Model\Data\CountryInformation');
        $this->countryInformationFactory->expects($this->once())->method('create')->willReturn($countryInfo);

        $regionInfo = $this->objectManager->getObject('\Magento\Directory\Model\Data\RegionInformation');
        $this->regionInformationFactory->expects($this->once())->method('create')->willReturn($regionInfo);

        $result = $this->model->getCountriesInfo();
        $this->assertEquals('US', $result[0]->getId());
        $this->assertEquals('US', $result[0]->getTwoLetterAbbreviation());
        $this->assertEquals('USA', $result[0]->getThreeLetterAbbreviation());
        $this->assertEquals('United States of America', $result[0]->getFullNameLocale());
        $this->assertEquals('United States of America', $result[0]->getFullNameEnglish());

        $regionResult = $result[0]->getAvailableRegions();
        $this->assertEquals('TX', $regionResult[0]->getCode());
        $this->assertEquals('Texas', $regionResult[0]->getName());
    }

    /**
     * test GetGetCountryInfo
     */
    public function testGetCountryInfo()
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManager->expects($this->once())->method('getStore')->willReturn($store);

        $testCountryInfo = $this->objectManager->getObject('\Magento\Directory\Model\Country');
        $testCountryInfo->setData('country_id', 'AE');
        $testCountryInfo->setData('iso2_code', 'AE');
        $testCountryInfo->setData('iso3_code', 'ARE');
        $testCountryInfo->setData('name_default', 'United Arab Emirates');
        $testCountryInfo->setData('name_en_US', 'United Arab Emirates');

        $countryCollection = $this->getMock(
            '\Magento\Directory\Model\ResourceModel\Country\Collection',
            [],
            [],
            '',
            false
        );
        $countryCollection->expects($this->once())->method('addCountryIdFilter')->willReturnSelf();
        $countryCollection->expects($this->once())->method('load')->willReturnSelf();
        $countryCollection->expects($this->once())->method('count')->willReturn(1);
        $countryCollection->expects($this->once())->method('getItemById')->with('AE')->willReturn($testCountryInfo);

        $this->directoryHelper->expects($this->once())->method('getCountryCollection')->willReturn($countryCollection);
        $this->directoryHelper->expects($this->once())->method('getRegionData')->willReturn([]);

        $countryInfo = $this->objectManager->getObject('\Magento\Directory\Model\Data\CountryInformation');
        $this->countryInformationFactory->expects($this->once())->method('create')->willReturn($countryInfo);

        $result = $this->model->getCountryInfo('AE');
        $this->assertEquals('AE', $result->getId());
        $this->assertEquals('AE', $result->getTwoLetterAbbreviation());
        $this->assertEquals('ARE', $result->getThreeLetterAbbreviation());
        $this->assertEquals('United Arab Emirates', $result->getFullNameLocale());
        $this->assertEquals('United Arab Emirates', $result->getFullNameEnglish());
    }

    /**
     * test GetGetCountryInfoNotFound
     *
     * @expectedException        \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Requested country is not available.
     */
    public function testGetCountryInfoNotFound()
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManager->expects($this->once())->method('getStore')->willReturn($store);

        $testCountryInfo = $this->objectManager->getObject('\Magento\Directory\Model\Country');
        $testCountryInfo->setData('country_id', 'AE');
        $testCountryInfo->setData('iso2_code', 'AE');
        $testCountryInfo->setData('iso3_code', 'ARE');
        $testCountryInfo->setData('name_default', 'United Arab Emirates');
        $testCountryInfo->setData('name_en_US', 'United Arab Emirates');

        $countryCollection = $this->getMock(
            '\Magento\Directory\Model\ResourceModel\Country\Collection',
            [],
            [],
            '',
            false
        );
        $countryCollection->expects($this->once())->method('addCountryIdFilter')->willReturnSelf();
        $countryCollection->expects($this->once())->method('load')->willReturnSelf();
        $countryCollection->expects($this->once())->method('count')->willReturn(0);

        $this->directoryHelper->expects($this->once())->method('getCountryCollection')->willReturn($countryCollection);
        $this->model->getCountryInfo('AE');
    }
}
