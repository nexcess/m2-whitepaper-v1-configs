<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\UrlRewrite\Model\Storage;

use Magento\Framework\App\ResourceConnection;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\Framework\Api\DataObjectHelper;

class DbStorage extends AbstractStorage
{
    /**
     * DB Storage table name
     */
    const TABLE_NAME = 'url_rewrite';

    /**
     * Code of "Integrity constraint violation: 1062 Duplicate entry" error
     */
    const ERROR_CODE_DUPLICATE_ENTRY = 23000;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory $urlRewriteFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        UrlRewriteFactory $urlRewriteFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceConnection $resource
    ) {
        $this->connection = $resource->getConnection();
        $this->resource = $resource;

        parent::__construct($urlRewriteFactory, $dataObjectHelper);
    }

    /**
     * Prepare select statement for specific filter
     *
     * @param array $data
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelect($data)
    {
        $select = $this->connection->select();
        $select->from($this->resource->getTableName(self::TABLE_NAME));

        foreach ($data as $column => $value) {
            $select->where($this->connection->quoteIdentifier($column) . ' IN (?)', $value);
        }
        return $select;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindAllByData($data)
    {
        return $this->connection->fetchAll($this->prepareSelect($data));
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindOneByData($data)
    {
        return $this->connection->fetchRow($this->prepareSelect($data));
    }

    /**
     * {@inheritdoc}
     */
    protected function doReplace($urls)
    {
        foreach ($this->createFilterDataBasedOnUrls($urls) as $type => $urlData) {
            $urlData[UrlRewrite::ENTITY_TYPE] = $type;
            $this->deleteByData($urlData);
        }
        $data = [];
        foreach ($urls as $url) {
            $data[] = $url->toArray();
        }
        $this->insertMultiple($data);
    }

    /**
     * Insert multiple
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Exception
     */
    protected function insertMultiple($data)
    {
        try {
            $this->connection->insertMultiple($this->resource->getTableName(self::TABLE_NAME), $data);
        } catch (\Exception $e) {
            if ($e->getCode() === self::ERROR_CODE_DUPLICATE_ENTRY
                && preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\d]#', $e->getMessage())
            ) {
                throw new \Magento\Framework\Exception\AlreadyExistsException(
                    __('URL key for specified store already exists.')
                );
            }
            throw $e;
        }
    }

    /**
     * Get filter for url rows deletion due to provided urls
     *
     * @param UrlRewrite[] $urls
     * @return array
     */
    protected function createFilterDataBasedOnUrls($urls)
    {
        $data = [];
        foreach ($urls as $url) {
            $entityType = $url->getEntityType();
            foreach ([UrlRewrite::ENTITY_ID, UrlRewrite::STORE_ID] as $key) {
                $fieldValue = $url->getByKey($key);
                if (!isset($data[$entityType][$key]) || !in_array($fieldValue, $data[$entityType][$key])) {
                    $data[$entityType][$key][] = $fieldValue;
                }
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByData(array $data)
    {
        $this->connection->query(
            $this->prepareSelect($data)->deleteFromSelect($this->resource->getTableName(self::TABLE_NAME))
        );
    }
}
