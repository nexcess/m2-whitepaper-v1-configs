<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Model\Indexer\Fulltext\Action;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class IndexIterator implements \Iterator
{
    /**
     * @var \Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider
     */
    private $dataProvider;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var array
     */
    private $staticFields;

    /**
     * @var array
     */
    private $productIds;

    /**
     * @var array
     */
    private $dynamicFields;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    private $visibility;

    /**
     * @var array
     */
    private $allowedVisibility;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    private $status;

    /**
     * @var array
     */
    private $statusIds;

    /**
     * @var int
     */
    private $lastProductId = 0;

    /**
     * @var array
     */
    private $products = [];

    /**
     * @var null
     */
    private $current = null;

    /**
     * @var bool
     */
    private $isValid = true;

    /**
     * @var null
     */
    private $key = null;

    /**
     * @var array
     */
    private $productAttributes = [];

    /**
     * @var array
     */
    private $productRelations = [];

    /**
     * Initialize dependencies.
     *
     * @param DataProvider $dataProvider
     * @param int $storeId
     * @param array $staticFields
     * @param array|null $productIds
     * @param array $dynamicFields
     * @param \Magento\Eav\Model\Entity\Attribute $visibility
     * @param array $allowedVisibility
     * @param \Magento\Eav\Model\Entity\Attribute $status
     * @param array $statusIds
     *
     * @SuppressWarnings(Magento.TypeDuplication)
     */
    public function __construct(
        DataProvider $dataProvider,
        $storeId,
        array $staticFields,
        $productIds,
        array $dynamicFields,
        \Magento\Eav\Model\Entity\Attribute $visibility,
        array $allowedVisibility,
        \Magento\Eav\Model\Entity\Attribute $status,
        array $statusIds
    ) {
        $this->dataProvider = $dataProvider;
        $this->storeId = $storeId;
        $this->staticFields = $staticFields;
        $this->productIds = $productIds;
        $this->dynamicFields = $dynamicFields;
        $this->visibility = $visibility;
        $this->allowedVisibility = $allowedVisibility;
        $this->status = $status;
        $this->statusIds = $statusIds;
    }


    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        \next($this->products);
        if (\key($this->products) === null) {
            // check if storage has more items to process
            $this->products = $this->dataProvider->getSearchableProducts(
                $this->storeId,
                $this->staticFields,
                $this->productIds,
                $this->lastProductId
            );

            if (!count($this->products)) {
                $this->isValid = false;
                return;
            }

            $productAttributes = [];
            $this->productRelations = [];
            foreach ($this->products as $productData) {
                $this->lastProductId = $productData['entity_id'];
                $productAttributes[$productData['entity_id']] = $productData['entity_id'];
                $productChildren = $this->dataProvider->getProductChildIds(
                    $productData['entity_id'],
                    $productData['type_id']
                );
                $this->productRelations[$productData['entity_id']] = $productChildren;
                if ($productChildren) {
                    foreach ($productChildren as $productChildId) {
                        $productAttributes[$productChildId] = $productChildId;
                    }
                }
            }
            \reset($this->products);

            $this->productAttributes = $this->dataProvider->getProductAttributes(
                $this->storeId,
                $productAttributes,
                $this->dynamicFields
            );
        }

        $productData = \current($this->products);

        if (!isset($this->productAttributes[$productData['entity_id']])) {
            $this->next();
            return;
        }

        $productAttr = $this->productAttributes[$productData['entity_id']];
        if (!isset($productAttr[$this->visibility->getId()])
            || !in_array($productAttr[$this->visibility->getId()], $this->allowedVisibility)
        ) {
            $this->next();
            return;
        }
        if (!isset($productAttr[$this->status->getId()])
            || !in_array($productAttr[$this->status->getId()], $this->statusIds)
        ) {
            $this->next();
            return;
        }

        $productIndex = [$productData['entity_id'] => $productAttr];

        $hasChildren = false;
        $productChildren = $this->productRelations[$productData['entity_id']];
        if ($productChildren) {
            foreach ($productChildren as $productChildId) {
                if (isset($this->productAttributes[$productChildId])) {
                    $productChildAttr = $this->productAttributes[$productChildId];
                    if (!isset($productChildAttr[$this->status->getId()])
                        || !in_array($productChildAttr[$this->status->getId()], $this->statusIds)
                    ) {
                        continue;
                    }

                    $hasChildren = true;
                    $productIndex[$productChildId] = $productChildAttr;
                }
            }
        }
        if ($productChildren !== null && !$hasChildren) {
            $this->next();
            return;
        }

        $index = $this->dataProvider->prepareProductIndex($productIndex, $productData, $this->storeId);

        $this->current = $index;
        $this->key = $productData['entity_id'];
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return $this->isValid;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->lastProductId = 0;
        $this->key = null;
        $this->current = null;
        unset($this->products);
        $this->products = [];
        $this->next();
    }
}
