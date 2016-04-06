<?php
namespace Magento\Theme\Helper\Storage;

/**
 * Proxy class for @see \Magento\Theme\Helper\Storage
 */
class Proxy extends \Magento\Theme\Helper\Storage implements \Magento\Framework\ObjectManager\NoninterceptableInterface
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
     * @var \Magento\Theme\Helper\Storage
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\Theme\\Helper\\Storage', $shared = true)
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
     * @return \Magento\Theme\Helper\Storage
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
    public function convertPathToId($path)
    {
        return $this->_getSubject()->convertPathToId($path);
    }

    /**
     * {@inheritdoc}
     */
    public function convertIdToPath($value)
    {
        return $this->_getSubject()->convertIdToPath($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortFilename($filename, $maxLength = 20)
    {
        return $this->_getSubject()->getShortFilename($filename, $maxLength);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageRoot()
    {
        return $this->_getSubject()->getStorageRoot();
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageType()
    {
        return $this->_getSubject()->getStorageType();
    }

    /**
     * {@inheritdoc}
     */
    public function getRelativeUrl()
    {
        return $this->_getSubject()->getRelativeUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPath()
    {
        return $this->_getSubject()->getCurrentPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnailDirectory($path)
    {
        return $this->_getSubject()->getThumbnailDirectory($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnailPath($imageName)
    {
        return $this->_getSubject()->getThumbnailPath($imageName);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestParams()
    {
        return $this->_getSubject()->getRequestParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedExtensionsByType()
    {
        return $this->_getSubject()->getAllowedExtensionsByType();
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageTypeName()
    {
        return $this->_getSubject()->getStorageTypeName();
    }

    /**
     * {@inheritdoc}
     */
    public function getSession()
    {
        return $this->_getSubject()->getSession();
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        return $this->_getSubject()->isModuleOutputEnabled($moduleName);
    }
}
