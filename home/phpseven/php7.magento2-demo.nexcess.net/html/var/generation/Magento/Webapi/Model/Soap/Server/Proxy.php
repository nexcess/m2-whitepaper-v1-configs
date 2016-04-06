<?php
namespace Magento\Webapi\Model\Soap\Server;

/**
 * Proxy class for @see \Magento\Webapi\Model\Soap\Server
 */
class Proxy extends \Magento\Webapi\Model\Soap\Server implements \Magento\Framework\ObjectManager\NoninterceptableInterface
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
     * @var \Magento\Webapi\Model\Soap\Server
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\Webapi\\Model\\Soap\\Server', $shared = true)
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
     * @return \Magento\Webapi\Model\Soap\Server
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
    public function handle()
    {
        return $this->_getSubject()->handle();
    }

    /**
     * {@inheritdoc}
     */
    public function getApiCharset()
    {
        return $this->_getSubject()->getApiCharset();
    }

    /**
     * {@inheritdoc}
     */
    public function generateUri($isWsdl = false)
    {
        return $this->_getSubject()->generateUri($isWsdl);
    }

    /**
     * {@inheritdoc}
     */
    public function getEndpointUri()
    {
        return $this->_getSubject()->getEndpointUri();
    }
}
