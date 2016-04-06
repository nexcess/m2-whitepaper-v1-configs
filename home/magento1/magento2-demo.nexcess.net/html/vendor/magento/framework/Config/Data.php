<?php
/**
 * Config data. Represents loaded and cached configuration data. Should be used to gain access to different types
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Config;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Data implements \Magento\Framework\Config\DataInterface
{
    /**
     * Configuration reader model
     *
     * @var ReaderInterface
     */
    protected $_reader;

    /**
     * Configuration cache model
     *
     * @var CacheInterface
     */
    protected $_cache;

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Cache tags
     *
     * @var array
     */
    protected $cacheTags = [];

    /**
     * Config data
     *
     * @var array
     */
    protected $_data = [];

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheId;

    /**
     * Constructor
     *
     * @param ReaderInterface $reader
     * @param CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        ReaderInterface $reader,
        CacheInterface $cache,
        $cacheId
    ) {
        $this->reader = $reader;
        $this->cache = $cache;
        $this->cacheId = $cacheId;
        $this->initData();
    }

    /**
     * Initialise data for configuration
     * @return void
     */
    protected function initData()
    {
        $data = $this->cache->load($this->cacheId);
        if (false === $data) {
            $data = $this->reader->read();
            $this->cache->save(serialize($data), $this->cacheId, $this->cacheTags);
        } else {
            $data = unserialize($data);
        }
        $this->merge($data);
    }

    /**
     * Merge config data to the object
     *
     * @param array $config
     * @return void
     */
    public function merge(array $config)
    {
        $this->_data = array_replace_recursive($this->_data, $config);
    }

    /**
     * Get config value by key
     *
     * @param string $path
     * @param mixed $default
     * @return array|mixed|null
     */
    public function get($path = null, $default = null)
    {
        if ($path === null) {
            return $this->_data;
        }
        $keys = explode('/', $path);
        $data = $this->_data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return $default;
            }
        }
        return $data;
    }

    /**
     * Clear cache data
     * @return void
     */
    public function reset()
    {
        $this->cache->remove($this->cacheId);
    }
}
