<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Abstract class for the controller tests
 */
namespace Magento\TestFramework\TestCase;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractController extends \PHPUnit_Framework_TestCase
{
    protected $_runCode = '';

    protected $_runScope = 'store';

    protected $_runOptions = [];

    /**
     * @var \Magento\TestFramework\Request
     */
    protected $_request;

    /**
     * @var \Magento\TestFramework\Response
     */
    protected $_response;

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Whether absence of session error messages has to be asserted automatically upon a test completion
     *
     * @var bool
     */
    protected $_assertSessionErrors = false;

    /**
     * Bootstrap instance getter
     *
     * @return \Magento\TestFramework\Helper\Bootstrap
     */
    protected function _getBootstrap()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getInstance();
    }

    /**
     * Bootstrap application before any test
     */
    protected function setUp()
    {
        $this->_assertSessionErrors = false;
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_objectManager->removeSharedInstance('Magento\Framework\App\ResponseInterface');
        $this->_objectManager->removeSharedInstance('Magento\Framework\App\RequestInterface');
    }

    protected function tearDown()
    {
        $this->_request = null;
        $this->_response = null;
        $this->_objectManager = null;
    }

    /**
     * Ensure that there were no error messages displayed on the admin panel
     */
    protected function assertPostConditions()
    {
        if ($this->_assertSessionErrors) {
            // equalTo() is intentionally used instead of isEmpty() to provide the informative diff
            $this->assertSessionMessages(
                $this->equalTo([]),
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
        }
    }

    /**
     * Run request
     *
     * @param string $uri
     */
    public function dispatch($uri)
    {
        $this->getRequest()->setRequestUri($uri);
        $this->_getBootstrap()->runApp();
    }

    /**
     * Request getter
     *
     * @return \Magento\TestFramework\Request
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = $this->_objectManager->get('Magento\Framework\App\RequestInterface');
        }
        return $this->_request;
    }

    /**
     * Response getter
     *
     * @return \Magento\TestFramework\Response
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $this->_response = $this->_objectManager->get('Magento\Framework\App\ResponseInterface');
        }
        return $this->_response;
    }

    /**
     * Assert that response is '404 Not Found'
     */
    public function assert404NotFound()
    {
        $this->assertEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertContains('404 Not Found', $this->getResponse()->getBody());
    }

    /**
     * Analyze response object and look for header with specified name, and assert a regex towards its value
     *
     * @param string $headerName
     * @param string $valueRegex
     * @throws \PHPUnit_Framework_AssertionFailedError when header not found
     */
    public function assertHeaderPcre($headerName, $valueRegex)
    {
        $headerFound = false;
        $headers = $this->getResponse()->getHeaders();
        foreach ($headers as $header) {
            if ($header->getFieldName() === $headerName) {
                $headerFound = true;
                $this->assertRegExp($valueRegex, $header->getFieldValue());
            }
        }
        if (!$headerFound) {
            $this->fail("Header '{$headerName}' was not found. Headers dump:\n" . var_export($headers, 1));
        }
    }

    /**
     * Assert that there is a redirect to expected URL.
     * Omit expected URL to check that redirect to wherever has been occurred.
     * Examples of usage:
     * $this->assertRedirect($this->equalTo($expectedUrl));
     * $this->assertRedirect($this->stringStartsWith($expectedUrlPrefix));
     * $this->assertRedirect($this->stringEndsWith($expectedUrlSuffix));
     * $this->assertRedirect($this->stringContains($expectedUrlSubstring));
     *
     * @param \PHPUnit_Framework_Constraint|null $urlConstraint
     */
    public function assertRedirect(\PHPUnit_Framework_Constraint $urlConstraint = null)
    {
        $this->assertTrue($this->getResponse()->isRedirect(), 'Redirect was expected, but none was performed.');
        if ($urlConstraint) {
            $actualUrl = '';
            foreach ($this->getResponse()->getHeaders() as $header) {
                if ($header->getFieldName() == 'Location') {
                    $actualUrl = $header->getFieldValue();
                    break;
                }
            }
            $this->assertThat($actualUrl, $urlConstraint, 'Redirection URL does not match expectations');
        }
    }

    /**
     * Assert that actual session messages meet expectations:
     * Usage examples:
     * $this->assertSessionMessages($this->isEmpty(), \Magento\Framework\Message\MessageInterface::TYPE_ERROR);
     * $this->assertSessionMessages($this->equalTo(array('Entity has been saved.')),
     * \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS);
     *
     * @param \PHPUnit_Framework_Constraint $constraint Constraint to compare actual messages against
     * @param string|null $messageType Message type filter,
     *        one of the constants \Magento\Framework\Message\MessageInterface::*
     * @param string $messageManagerClass Class of the session model that manages messages
     */
    public function assertSessionMessages(
        \PHPUnit_Framework_Constraint $constraint,
        $messageType = null,
        $messageManagerClass = 'Magento\Framework\Message\Manager'
    ) {
        $this->_assertSessionErrors = false;
        /** @var $messageManager \Magento\Framework\Message\ManagerInterface */
        $messageManager = $this->_objectManager->get($messageManagerClass);
        /** @var $messages \Magento\Framework\Message\AbstractMessage[] */
        if ($messageType === null) {
            $messages = $messageManager->getMessages()->getItems();
        } else {
            $messages = $messageManager->getMessages()->getItemsByType($messageType);
        }
        $actualMessages = [];
        foreach ($messages as $message) {
            $actualMessages[] = $message->getText();
        }
        $this->assertThat(
            $actualMessages,
            $constraint,
            'Session messages do not meet expectations' . var_export($actualMessages, true)
        );
    }
}
