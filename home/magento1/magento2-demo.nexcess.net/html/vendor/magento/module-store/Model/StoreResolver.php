<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Store\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreCookieManagerInterface;

class StoreResolver implements \Magento\Store\Api\StoreResolverInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'store_relations';

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var StoreCookieManagerInterface
     */
    protected $storeCookieManager;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    protected $cache;

    /**
     * @var StoreResolver\ReaderList
     */
    protected $readerList;

    /**
     * @var string
     */
    protected $runMode;

    /**
     * @var string
     */
    protected $scopeCode;

    /*
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Cache\FrontendInterface $cache
     * @param StoreResolver\ReaderList $readerList
     * @param string $runMode
     * @param null $scopeCode
     */
    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        StoreCookieManagerInterface $storeCookieManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Cache\FrontendInterface $cache,
        StoreResolver\ReaderList $readerList,
        $runMode = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        $this->storeRepository = $storeRepository;
        $this->storeCookieManager = $storeCookieManager;
        $this->request = $request;
        $this->cache = $cache;
        $this->readerList = $readerList;
        $this->runMode = $scopeCode ? $runMode : ScopeInterface::SCOPE_WEBSITE;
        $this->scopeCode = $scopeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStoreId()
    {
        list($stores, $defaultStoreId) = $this->getStoresData();

        $storeCode = $this->request->getParam(self::PARAM_NAME, $this->storeCookieManager->getStoreCodeFromCookie());
        if ($storeCode) {
            try {
                $store = $this->getRequestedStoreByCode($storeCode);
            } catch (NoSuchEntityException $e) {
                $store = $this->getDefaultStoreById($defaultStoreId);
            }

            if (!in_array($store->getId(), $stores)) {
                $store = $this->getDefaultStoreById($defaultStoreId);
            }
        } else {
            $store = $this->getDefaultStoreById($defaultStoreId);
        }
        return $store->getId();
    }

    /**
     * Get stores data
     *
     * @return array
     */
    protected function getStoresData()
    {
        $cacheKey = 'resolved_stores_' . md5($this->runMode . $this->scopeCode);
        $cacheData = $this->cache->load($cacheKey);
        if ($cacheData) {
            $storesData = unserialize($cacheData);
        } else {
            $storesData = $this->readStoresData();
            $this->cache->save(serialize($storesData), $cacheKey, [self::CACHE_TAG]);
        }
        return $storesData;
    }

    /**
     * Read stores data. First element is allowed store ids, second is default store id
     *
     * @return array
     */
    protected function readStoresData()
    {
        $reader = $this->readerList->getReader($this->runMode);
        return [$reader->getAllowedStoreIds($this->scopeCode), $reader->getDefaultStoreId($this->scopeCode)];
    }

    /**
     * Retrieve active store by code
     *
     * @param string $storeCode
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws NoSuchEntityException
     */
    protected function getRequestedStoreByCode($storeCode)
    {
        try {
            $store = $this->storeRepository->getActiveStoreByCode($storeCode);
        } catch (StoreIsInactiveException $e) {
            throw new NoSuchEntityException(__('Requested store is inactive'));
        }

        return $store;
    }

    /**
     * Retrieve active store by code
     *
     * @param int $id
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws NoSuchEntityException
     */
    protected function getDefaultStoreById($id)
    {
        try {
            $store = $this->storeRepository->getActiveStoreById($id);
        } catch (StoreIsInactiveException $e) {
            throw new NoSuchEntityException(__('Default store is inactive'));
        }

        return $store;
    }
}
