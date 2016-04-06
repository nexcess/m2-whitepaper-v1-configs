<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Model\ResourceModel;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkInterface;
use Magento\Ui\Model\ResourceModel\Bookmark\Collection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BookmarkRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BookmarkRepository implements BookmarkRepositoryInterface
{
    /**
     * @var \Magento\Ui\Api\Data\BookmarkInterfaceFactory
     */
    protected $bookmarkFactory;

    /**
     * @var \Magento\Ui\Model\ResourceModel\Bookmark
     */
    protected $bookmarkResourceModel;

    /**
     * @var \Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @param \Magento\Ui\Api\Data\BookmarkInterfaceFactory $bookmarkFactory
     * @param Bookmark $bookmarkResourceModel
     * @param \Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \Magento\Ui\Api\Data\BookmarkInterfaceFactory $bookmarkFactory,
        \Magento\Ui\Model\ResourceModel\Bookmark $bookmarkResourceModel,
        \Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory $searchResultsFactory
    ) {

        $this->bookmarkResourceModel = $bookmarkResourceModel;
        $this->bookmarkFactory = $bookmarkFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save bookmark.
     *
     * @param BookmarkInterface $bookmark
     * @return BookmarkInterface
     * @throws CouldNotSaveException
     */
    public function save(BookmarkInterface $bookmark)
    {
        try {
            $this->bookmarkResourceModel->save($bookmark);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $bookmark;
    }

    /**
     * Retrieve bookmark.
     *
     * @param int $bookmarkId
     * @return BookmarkInterface
     * @throws NoSuchEntityException
     */
    public function getById($bookmarkId)
    {
        $bookmark = $this->bookmarkFactory->create();
        $this->bookmarkResourceModel->load($bookmark, $bookmarkId);
        if (!$bookmark->getId()) {
            throw new NoSuchEntityException(__('Bookmark with id "%1" does not exist.', $bookmarkId));
        }
        return $bookmark;
    }

    /**
     * Retrieve bookmarks matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Magento\Ui\Model\ResourceModel\Bookmark\Collection $collection */
        $collection = $this->bookmarkFactory->create()->getCollection();
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $bookmarks = [];
        /** @var BookmarkInterface $bookmark */
        foreach ($collection->getItems() as $bookmark) {
            $bookmarks[] = $this->getById($bookmark->getId());
        }
        $searchResults->setItems($bookmarks);

        return $searchResults;
    }

    /**
     * Delete bookmark.
     *
     * @param BookmarkInterface $bookmark
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(BookmarkInterface $bookmark)
    {
        try {
            $this->bookmarkResourceModel->delete($bookmark);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete bookmark by ID.
     *
     * @param int $bookmarkId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById($bookmarkId)
    {
        return $this->delete($this->getById($bookmarkId));
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }
}
