<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sitemap data helper
 *
 */
namespace Magento\Sitemap\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config path to sitemap valid paths
     */
    const XML_PATH_SITEMAP_VALID_PATHS = 'sitemap/file/valid_paths';

    /**
     * Config path to valid file paths
     */
    const XML_PATH_PUBLIC_FILES_VALID_PATHS = 'general/file/public_files_valid_paths';

    /**#@+
     * Limits xpath config settings
     */
    const XML_PATH_MAX_LINES = 'sitemap/limit/max_lines';

    const XML_PATH_MAX_FILE_SIZE = 'sitemap/limit/max_file_size';

    /**#@-*/

    /**#@+
     * Change frequency xpath config settings
     */
    const XML_PATH_CATEGORY_CHANGEFREQ = 'sitemap/category/changefreq';

    const XML_PATH_PRODUCT_CHANGEFREQ = 'sitemap/product/changefreq';

    const XML_PATH_PAGE_CHANGEFREQ = 'sitemap/page/changefreq';

    /**#@-*/

    /**#@+
     * Change frequency xpath config settings
     */
    const XML_PATH_CATEGORY_PRIORITY = 'sitemap/category/priority';

    const XML_PATH_PRODUCT_PRIORITY = 'sitemap/product/priority';

    const XML_PATH_PAGE_PRIORITY = 'sitemap/page/priority';

    /**#@-*/

    /**#@+
     * Search Engine Submission Settings
     */
    const XML_PATH_SUBMISSION_ROBOTS = 'sitemap/search_engines/submission_robots';

    /**#@-*/
    const XML_PATH_PRODUCT_IMAGES_INCLUDE = 'sitemap/product/image_include';

    /**
     * Get maximum sitemap.xml URLs number
     *
     * @param int $storeId
     * @return int
     */
    public function getMaximumLinesNumber($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAX_LINES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get maximum sitemap.xml file size in bytes
     *
     * @param int $storeId
     * @return int
     */
    public function getMaximumFileSize($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAX_FILE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get category change frequency
     *
     * @param int $storeId
     * @return string
     */
    public function getCategoryChangefreq($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_CHANGEFREQ,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get product change frequency
     *
     * @param int $storeId
     * @return string
     */
    public function getProductChangefreq($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_CHANGEFREQ,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get page change frequency
     *
     * @param int $storeId
     * @return string
     */
    public function getPageChangefreq($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_PAGE_CHANGEFREQ,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get category priority
     *
     * @param int $storeId
     * @return string
     */
    public function getCategoryPriority($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_PRIORITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get product priority
     *
     * @param int $storeId
     * @return string
     */
    public function getProductPriority($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_PRIORITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get page priority
     *
     * @param int $storeId
     * @return string
     */
    public function getPagePriority($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_PAGE_PRIORITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get enable Submission to Robots.txt
     *
     * @param int $storeId
     * @return int
     */
    public function getEnableSubmissionRobots($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SUBMISSION_ROBOTS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get product image include policy
     *
     * @param int $storeId
     * @return string
     */
    public function getProductImageIncludePolicy($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_IMAGES_INCLUDE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get list valid paths for generate a sitemap XML file
     *
     * @return string[]
     */
    public function getValidPaths()
    {
        return array_merge(
            $this->scopeConfig->getValue(self::XML_PATH_SITEMAP_VALID_PATHS, ScopeInterface::SCOPE_STORE),
            $this->scopeConfig->getValue(self::XML_PATH_PUBLIC_FILES_VALID_PATHS, ScopeInterface::SCOPE_STORE)
        );
    }
}
