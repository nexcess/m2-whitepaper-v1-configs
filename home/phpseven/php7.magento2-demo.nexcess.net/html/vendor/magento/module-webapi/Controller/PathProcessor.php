<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Webapi\Controller;

use Magento\Framework\Exception\NoSuchEntityException;

class PathProcessor
{
    /**  Store code alias to indicate that all stores should be affected by action */
    const ALL_STORE_CODE = 'all';

    /**  @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Process path
     *
     * @param string $pathInfo
     * @return array
     */
    private function stripPathBeforeStorecode($pathInfo)
    {
        $pathParts = explode('/', trim($pathInfo, '/'));
        array_shift($pathParts);
        $path = '/' . implode('/', $pathParts);
        return explode('/', ltrim($path, '/'), 2);
    }

    /**
     * Process path info
     *
     * @param string $pathInfo
     * @return string
     * @throws NoSuchEntityException
     */
    public function process($pathInfo)
    {
        $pathParts = $this->stripPathBeforeStorecode($pathInfo);
        $storeCode = $pathParts[0];
        $stores = $this->storeManager->getStores(false, true);
        if (isset($stores[$storeCode])) {
            $this->storeManager->setCurrentStore($storeCode);
            $path = '/' . (isset($pathParts[1]) ? $pathParts[1] : '');
        } else if ($storeCode === self::ALL_STORE_CODE) {
            $this->storeManager->setCurrentStore(\Magento\Store\Model\Store::ADMIN_CODE);
            $path = '/' . (isset($pathParts[1]) ? $pathParts[1] : '');
        } else {
            $path = '/' . implode('/', $pathParts);
        }
        return $path;
    }
}
