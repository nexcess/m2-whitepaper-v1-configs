<?php
namespace Magento\Framework\Translate\InlineInterface;

/**
 * Proxy class for @see \Magento\Framework\Translate\InlineInterface
 */
class Proxy implements \Magento\Framework\Translate\InlineInterface, \Magento\Framework\ObjectManager\NoninterceptableInterface
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
     * @var \Magento\Framework\Translate\InlineInterface
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\Framework\\Translate\\InlineInterface', $shared = true)
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
     * @return \Magento\Framework\Translate\InlineInterface
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
    public function getAdditionalHtmlAttribute($tagName = null)
    {
        return $this->_getSubject()->getAdditionalHtmlAttribute($tagName);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return $this->_getSubject()->isAllowed();
    }

    /**
     * {@inheritdoc}
     */
    public function processResponseBody(&$body, $isJson = false)
    {
        return $this->_getSubject()->processResponseBody($body, $isJson);
    }

    /**
     * {@inheritdoc}
     */
    public function getParser()
    {
        return $this->_getSubject()->getParser();
    }
}
