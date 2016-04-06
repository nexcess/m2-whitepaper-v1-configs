<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Api;

/**
 * Bookmark CRUD interface.
 */
interface BookmarkRepositoryInterface
{
    /**
     * Save bookmark.
     *
     * @api
     * @param \Magento\Ui\Api\Data\BookmarkInterface $bookmark
     * @return \Magento\Ui\Api\Data\BookmarkInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\Ui\Api\Data\BookmarkInterface $bookmark);

    /**
     * Retrieve bookmark.
     *
     * @api
     * @param int $bookmarkId
     * @return \Magento\Ui\Api\Data\BookmarkInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($bookmarkId);

    /**
     * Retrieve bookmarks matching the specified criteria.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Ui\Api\Data\BookmarkSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete bookmark.
     *
     * @api
     * @param \Magento\Ui\Api\Data\BookmarkInterface $bookmark
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magento\Ui\Api\Data\BookmarkInterface $bookmark);

    /**
     * Delete bookmark by ID.
     *
     * @api
     * @param int $bookmarkId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($bookmarkId);
}
