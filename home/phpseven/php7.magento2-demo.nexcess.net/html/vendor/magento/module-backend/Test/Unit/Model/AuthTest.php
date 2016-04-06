<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class AuthTest
 */
class AuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_credentialStorage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelFactoryMock;

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMock('\Magento\Framework\Event\ManagerInterface');
        $this->_credentialStorage = $this->getMock('\Magento\Backend\Model\Auth\Credential\StorageInterface');
        $this->_modelFactoryMock = $this->getMock('\Magento\Framework\Data\Collection\ModelFactory', [], [], '', false);
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject(
            'Magento\Backend\Model\Auth',
            [
                'eventManager' => $this->_eventManagerMock,
                'credentialStorage' => $this->_credentialStorage,
                'modelFactory' => $this->_modelFactoryMock
            ]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\AuthenticationException
     * @expectedExceptionMessage You did not sign in correctly or your account is temporarily disabled.
     */
    public function testLoginFailed()
    {
        $this->_modelFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with('Magento\Backend\Model\Auth\Credential\StorageInterface')
            ->will($this->returnValue($this->_credentialStorage));
        $exceptionMock = new \Magento\Framework\Exception\LocalizedException(
            __('You did not sign in correctly or your account is temporarily disabled.')
        );
        $this->_credentialStorage
            ->expects($this->once())
            ->method('login')
            ->with('username', 'password')
            ->will($this->throwException($exceptionMock));
        $this->_credentialStorage->expects($this->never())->method('getId');
        $this->_eventManagerMock->expects($this->once())->method('dispatch')->with('backend_auth_user_login_failed');
        $this->_model->login('username', 'password');
    }
}
