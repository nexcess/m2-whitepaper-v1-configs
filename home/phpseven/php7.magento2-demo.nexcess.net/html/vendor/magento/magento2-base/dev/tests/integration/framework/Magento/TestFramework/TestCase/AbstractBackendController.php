<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\TestCase;

/**
 * A parent class for backend controllers - contains directives for admin user creation and authentication
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.numberOfChildren)
 */
abstract class AbstractBackendController extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_session;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * The resource used to authorize action
     *
     * @var string
     */
    protected $resource = null;

    /**
     * The uri at which to access the controller
     *
     * @var string
     */
    protected $uri = null;

    protected function setUp()
    {
        parent::setUp();

        $this->_objectManager->get('Magento\Backend\Model\UrlInterface')->turnOffSecretKey();

        $this->_auth = $this->_objectManager->get('Magento\Backend\Model\Auth');
        $this->_session = $this->_auth->getAuthStorage();
        $credentials = $this->_getAdminCredentials();
        $this->_auth->login($credentials['user'], $credentials['password']);
    }

    /**
     * Get credentials to login admin user
     *
     * @return array
     */
    protected function _getAdminCredentials()
    {
        return [
            'user' => \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        ];
    }

    protected function tearDown()
    {
        $this->_auth->getAuthStorage()->destroy(['send_expire_cookie' => false]);
        $this->_auth = null;
        $this->_session = null;
        $this->_objectManager->get('Magento\Backend\Model\UrlInterface')->turnOnSecretKey();
        parent::tearDown();
    }

    /**
     * Utilize backend session model by default
     *
     * @param \PHPUnit_Framework_Constraint $constraint
     * @param string|null $messageType
     * @param string $messageManagerClass
     */
    public function assertSessionMessages(
        \PHPUnit_Framework_Constraint $constraint,
        $messageType = null,
        $messageManagerClass = 'Magento\Framework\Message\Manager'
    ) {
        parent::assertSessionMessages($constraint, $messageType, $messageManagerClass);
    }


    public function testAclHasAccess()
    {
        if ($this->uri === null) {
            $this->markTestIncomplete('AclHasAccess test is not complete');
        }
        $this->dispatch($this->uri);
        $this->assertNotSame(403, $this->getResponse()->getHttpResponseCode());
        $this->assertNotSame(404, $this->getResponse()->getHttpResponseCode());
    }

    public function testAclNoAccess()
    {
        if ($this->resource === null) {
            $this->markTestIncomplete('Acl test is not complete');
        }
        $this->_objectManager->get('Magento\Framework\Acl\Builder')
            ->getAcl()
            ->deny(null, $this->resource);
        $this->dispatch($this->uri);
        $this->assertSame(403, $this->getResponse()->getHttpResponseCode());
    }
}
