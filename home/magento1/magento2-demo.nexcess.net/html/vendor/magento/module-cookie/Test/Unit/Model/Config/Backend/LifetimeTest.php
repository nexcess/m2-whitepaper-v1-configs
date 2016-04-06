<?php
/**
 * Unit test for Magento\Cookie\Model\Config\Backend\Lifetime
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Cookie\Test\Unit\Model\Config\Backend;

use Magento\Framework\Session\Config\Validator\CookieLifetimeValidator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class LifetimeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject | CookieLifetimeValidator */
    private $validatorMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Module\ModuleResource */
    private $resourceMock;

    /** @var \Magento\Cookie\Model\Config\Backend\Lifetime */
    private $model;

    public function setUp()
    {
        $this->validatorMock = $this->getMockBuilder(
            'Magento\Framework\Session\Config\Validator\CookieLifetimeValidator'
        )->disableOriginalConstructor()
            ->getMock();
        $this->resourceMock = $this->getMockBuilder('Magento\Framework\Module\ModuleResource')
            ->disableOriginalConstructor('delete')
            ->getMock();

        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Cookie\Model\Config\Backend\Lifetime',
            [
                'configValidator' => $this->validatorMock,
                'resource' => $this->resourceMock
            ]
        );
    }

    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Invalid cookie lifetime: must be numeric
     */
    public function testBeforeSaveException()
    {
        $invalidCookieLifetime = 'invalid lifetime';
        $messages = ['must be numeric'];
        $this->validatorMock->expects($this->once())
            ->method('getMessages')
            ->willReturn($messages);
        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($invalidCookieLifetime)
            ->willReturn(false);

        // Test
        $this->model->setValue($invalidCookieLifetime)->beforeSave();
    }

    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     * No assertions exist because the purpose of the test is to make sure that no
     * exception gets thrown
     */
    public function testBeforeSaveNoException()
    {
        $validCookieLifetime = 1;
        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($validCookieLifetime)
            ->willReturn(true);

        // Test
        $this->model->setValue($validCookieLifetime)->beforeSave();
    }

    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     * No assertions exist because the purpose of the test is to make sure that no
     * exception gets thrown
     */
    public function testBeforeEmptyString()
    {
        $validCookieLifetime = '';
        $this->validatorMock->expects($this->never())
            ->method('isValid');

        // Test
        $this->model->setValue($validCookieLifetime)->beforeSave();
    }
}
