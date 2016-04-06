<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo;
use Magento\Framework\Stdlib\DateTime;

/**
 * Customer personal information template block test.
 */
class PersonalInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $defaultTimezone = 'America/Los_Angeles';

    /**
     * @var string
     */
    protected $pathToDefaultTimezone = 'path/to/default/timezone';

    /**
     * @var PersonalInfo
     */
    protected $block;

    /**
     * @var \Magento\Customer\Model\Log|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerLog;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    /**
     * @return void
     */
    protected function setUp()
    {
        $customer = $this->getMock(
            'Magento\Customer\Api\Data\CustomerInterface',
            [],
            [],
            '',
            false
        );
        $customer->expects($this->any())->method('getId')->willReturn(1);
        $customer->expects($this->any())->method('getStoreId')->willReturn(1);

        $customerDataFactory = $this->getMock(
            'Magento\Customer\Api\Data\CustomerInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $customerDataFactory->expects($this->any())->method('create')->willReturn($customer);

        $backendSession = $this->getMock(
            'Magento\Backend\Model\Session',
            ['getCustomerData'],
            [],
            '',
            false
        );
        $backendSession->expects($this->any())->method('getCustomerData')->willReturn(['account' => []]);

        $this->customerLog = $this->getMock(
            'Magento\Customer\Model\Log',
            ['getLastLoginAt', 'getLastVisitAt', 'getLastLogoutAt'],
            [],
            '',
            false
        );
        $this->customerLog->expects($this->any())->method('loadByCustomer')->willReturnSelf();

        $customerLogger = $this->getMock(
            'Magento\Customer\Model\Logger',
            ['get'],
            [],
            '',
            false
        );
        $customerLogger->expects($this->any())->method('get')->willReturn($this->customerLog);

        $dateTime = $this->getMock(
            'Magento\Framework\Stdlib\DateTime',
            ['now'],
            [],
            '',
            false
        );
        $dateTime->expects($this->any())->method('now')->willReturn('2015-03-04 12:00:00');

        $this->localeDate = $this->getMock(
            'Magento\Framework\Stdlib\DateTime\Timezone',
            ['scopeDate', 'formatDateTime', 'getDefaultTimezonePath'],
            [],
            '',
            false
        );
        $this->localeDate
            ->expects($this->any())
            ->method('getDefaultTimezonePath')
            ->willReturn($this->pathToDefaultTimezone);

        $this->scopeConfig = $this->getMock(
            'Magento\Framework\App\Config',
            ['getValue'],
            [],
            '',
            false
        );

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->block = $objectManagerHelper->getObject(
            'Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo',
            [
                'customerDataFactory' => $customerDataFactory,
                'dateTime' => $dateTime,
                'customerLogger' => $customerLogger,
                'localeDate' => $this->localeDate,
                'scopeConfig' => $this->scopeConfig,
                'backendSession' => $backendSession,
            ]
        );
    }

    /**
     * @return void
     */
    public function testGetStoreLastLoginDateTimezone()
    {
        $this->scopeConfig
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->pathToDefaultTimezone,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
            ->willReturn($this->defaultTimezone);

        $this->assertEquals($this->defaultTimezone, $this->block->getStoreLastLoginDateTimezone());
    }

    /**
     * @param string $status
     * @param string|null $lastLoginAt
     * @param string|null $lastVisitAt
     * @param string|null $lastLogoutAt
     * @return void
     * @dataProvider getCurrentStatusDataProvider
     */
    public function testGetCurrentStatus($status, $lastLoginAt, $lastVisitAt, $lastLogoutAt)
    {
        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->with(
                'customer/online_customers/online_minutes_interval',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
            ->willReturn(240); //TODO: it's value mocked because unit tests run data providers before all testsuite

        $this->customerLog->expects($this->any())->method('getLastLoginAt')->willReturn($lastLoginAt);
        $this->customerLog->expects($this->any())->method('getLastVisitAt')->willReturn($lastVisitAt);
        $this->customerLog->expects($this->any())->method('getLastLogoutAt')->willReturn($lastLogoutAt);

        $this->assertEquals($status, (string) $this->block->getCurrentStatus());
    }

    /**
     * @return array
     */
    public function getCurrentStatusDataProvider()
    {
        return [
            ['Offline', null, null, null],
            ['Offline', '2015-03-04 11:00:00', null, '2015-03-04 12:00:00'],
            ['Offline', '2015-03-04 11:00:00', '2015-03-04 11:40:00', null],
            ['Online', '2015-03-04 11:00:00', (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT), null]
        ];
    }

    /**
     * @param string $result
     * @param string|null $lastLoginAt
     * @dataProvider getLastLoginDateDataProvider
     * @return void
     */
    public function testGetLastLoginDate($result, $lastLoginAt)
    {
        $this->customerLog->expects($this->once())->method('getLastLoginAt')->willReturn($lastLoginAt);
        $this->localeDate->expects($this->any())->method('formatDateTime')->willReturn($lastLoginAt);

        $this->assertEquals($result, $this->block->getLastLoginDate());
    }

    /**
     * @return array
     */
    public function getLastLoginDateDataProvider()
    {
        return [
            ['2015-03-04 12:00:00', '2015-03-04 12:00:00'],
            ['Never', null]
        ];
    }

    /**
     * @param string $result
     * @param string|null $lastLoginAt
     * @dataProvider getStoreLastLoginDateDataProvider
     * @return void
     */
    public function testGetStoreLastLoginDate($result, $lastLoginAt)
    {
        $this->customerLog->expects($this->once())->method('getLastLoginAt')->willReturn($lastLoginAt);

        $this->localeDate->expects($this->any())->method('scopeDate')->will($this->returnValue($lastLoginAt));
        $this->localeDate->expects($this->any())->method('formatDateTime')->willReturn($lastLoginAt);

        $this->assertEquals($result, $this->block->getStoreLastLoginDate());
    }

    /**
     * @return array
     */
    public function getStoreLastLoginDateDataProvider()
    {
        return [
            ['2015-03-04 12:00:00', '2015-03-04 12:00:00'],
            ['Never', null]
        ];
    }
}
