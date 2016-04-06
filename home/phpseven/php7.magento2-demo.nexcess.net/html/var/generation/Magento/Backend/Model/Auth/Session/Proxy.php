<?php
namespace Magento\Backend\Model\Auth\Session;

/**
 * Proxy class for @see \Magento\Backend\Model\Auth\Session
 */
class Proxy extends \Magento\Backend\Model\Auth\Session implements \Magento\Framework\ObjectManager\NoninterceptableInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\Backend\\Model\\Auth\\Session', $shared = true)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array('_subject', '_isShared');
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\Backend\Model\Auth\Session
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshAcl($user = null)
    {
        return $this->_getSubject()->refreshAcl($user);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($resource, $privilege = null)
    {
        return $this->_getSubject()->isAllowed($resource, $privilege);
    }

    /**
     * {@inheritdoc}
     */
    public function isLoggedIn()
    {
        return $this->_getSubject()->isLoggedIn();
    }

    /**
     * {@inheritdoc}
     */
    public function prolong()
    {
        return $this->_getSubject()->prolong();
    }

    /**
     * {@inheritdoc}
     */
    public function isFirstPageAfterLogin()
    {
        return $this->_getSubject()->isFirstPageAfterLogin();
    }

    /**
     * {@inheritdoc}
     */
    public function setIsFirstPageAfterLogin($value)
    {
        return $this->_getSubject()->setIsFirstPageAfterLogin($value);
    }

    /**
     * {@inheritdoc}
     */
    public function processLogin()
    {
        return $this->_getSubject()->processLogin();
    }

    /**
     * {@inheritdoc}
     */
    public function processLogout()
    {
        return $this->_getSubject()->processLogout();
    }

    /**
     * {@inheritdoc}
     */
    public function isValidForPath($path)
    {
        return $this->_getSubject()->isValidForPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function writeClose()
    {
        return $this->_getSubject()->writeClose();
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $args)
    {
        return $this->_getSubject()->__call($method, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        return $this->_getSubject()->start();
    }

    /**
     * {@inheritdoc}
     */
    public function isSessionExists()
    {
        return $this->_getSubject()->isSessionExists();
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $clear = false)
    {
        return $this->_getSubject()->getData($key, $clear);
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionId()
    {
        return $this->_getSubject()->getSessionId();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_getSubject()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->_getSubject()->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(array $options = null)
    {
        return $this->_getSubject()->destroy($options);
    }

    /**
     * {@inheritdoc}
     */
    public function clearStorage()
    {
        return $this->_getSubject()->clearStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieDomain()
    {
        return $this->_getSubject()->getCookieDomain();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookiePath()
    {
        return $this->_getSubject()->getCookiePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieLifetime()
    {
        return $this->_getSubject()->getCookieLifetime();
    }

    /**
     * {@inheritdoc}
     */
    public function setSessionId($sessionId)
    {
        return $this->_getSubject()->setSessionId($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionIdForHost($urlHost)
    {
        return $this->_getSubject()->getSessionIdForHost($urlHost);
    }

    /**
     * {@inheritdoc}
     */
    public function isValidForHost($host)
    {
        return $this->_getSubject()->isValidForHost($host);
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateId()
    {
        return $this->_getSubject()->regenerateId();
    }

    /**
     * {@inheritdoc}
     */
    public function expireSessionCookie()
    {
        return $this->_getSubject()->expireSessionCookie();
    }
}
