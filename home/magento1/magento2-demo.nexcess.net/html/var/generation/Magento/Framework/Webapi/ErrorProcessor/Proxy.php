<?php
namespace Magento\Framework\Webapi\ErrorProcessor;

/**
 * Proxy class for @see \Magento\Framework\Webapi\ErrorProcessor
 */
class Proxy extends \Magento\Framework\Webapi\ErrorProcessor implements \Magento\Framework\ObjectManager\NoninterceptableInterface
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
     * @var \Magento\Framework\Webapi\ErrorProcessor
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\Framework\\Webapi\\ErrorProcessor', $shared = true)
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
     * @return \Magento\Framework\Webapi\ErrorProcessor
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
    public function maskException(\Exception $exception)
    {
        return $this->_getSubject()->maskException($exception);
    }

    /**
     * {@inheritdoc}
     */
    public function renderException(\Exception $exception, $httpCode = 500)
    {
        return $this->_getSubject()->renderException($exception, $httpCode);
    }

    /**
     * {@inheritdoc}
     */
    public function renderErrorMessage($errorMessage, $trace = 'Trace is not available.', $httpCode = 500)
    {
        return $this->_getSubject()->renderErrorMessage($errorMessage, $trace, $httpCode);
    }

    /**
     * {@inheritdoc}
     */
    public function registerShutdownFunction()
    {
        return $this->_getSubject()->registerShutdownFunction();
    }

    /**
     * {@inheritdoc}
     */
    public function apiShutdownFunction()
    {
        return $this->_getSubject()->apiShutdownFunction();
    }
}
