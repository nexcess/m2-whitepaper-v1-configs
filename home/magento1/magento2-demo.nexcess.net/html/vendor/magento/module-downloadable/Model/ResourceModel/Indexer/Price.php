<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Model\ResourceModel\Indexer;

/**
 * Downloadable products Price indexer resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Price extends \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\DefaultPrice
{
    /**
     * Reindex temporary (price result data) for all products
     *
     * @throws \Exception
     * @return $this
     */
    public function reindexAll()
    {
        $this->tableStrategy->setUseIdxTable(true);
        $this->beginTransaction();
        try {
            $this->reindex();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     * @return $this
     */
    public function reindexEntity($entityIds)
    {
        return $this->reindex($entityIds);
    }

    /**
     * @param null|int|array $entityIds
     * @return \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\DefaultPrice
     */
    protected function reindex($entityIds = null)
    {
        if ($this->hasEntity() || !empty($entityIds)) {
            $this->_prepareFinalPriceData($entityIds);
            $this->_applyCustomOption();
            $this->_applyDownloadableLink();
            $this->_movePriceDataToIndexTable();
        }

        return $this;
    }

    /**
     * Retrieve downloadable links price temporary index table name
     *
     * @see _prepareDefaultFinalPriceTable()
     *
     * @return string
     */
    protected function _getDownloadableLinkPriceTable()
    {
        return $this->tableStrategy->getTableName('catalog_product_index_price_downlod');
    }

    /**
     * Prepare downloadable links price temporary index table
     *
     * @return $this
     */
    protected function _prepareDownloadableLinkPriceTable()
    {
        $this->getConnection()->delete($this->_getDownloadableLinkPriceTable());
        return $this;
    }

    /**
     * Calculate and apply Downloadable links price to index
     *
     * @return $this
     */
    protected function _applyDownloadableLink()
    {
        $connection = $this->getConnection();
        $table = $this->_getDownloadableLinkPriceTable();

        $this->_prepareDownloadableLinkPriceTable();

        $dlType = $this->_getAttribute('links_purchased_separately');

        $ifPrice = $connection->getIfNullSql('dlpw.price_id', 'dlpd.price');

        $select = $connection->select()->from(
            ['i' => $this->_getDefaultFinalPriceTable()],
            ['entity_id', 'customer_group_id', 'website_id']
        )->join(
            ['dl' => $dlType->getBackend()->getTable()],
            "dl.entity_id = i.entity_id AND dl.attribute_id = {$dlType->getAttributeId()}" . " AND dl.store_id = 0",
            []
        )->join(
            ['dll' => $this->getTable('downloadable_link')],
            'dll.product_id = i.entity_id',
            []
        )->join(
            ['dlpd' => $this->getTable('downloadable_link_price')],
            'dll.link_id = dlpd.link_id AND dlpd.website_id = 0',
            []
        )->joinLeft(
            ['dlpw' => $this->getTable('downloadable_link_price')],
            'dlpd.link_id = dlpw.link_id AND dlpw.website_id = i.website_id',
            []
        )->where(
            'dl.value = ?',
            1
        )->group(
            ['i.entity_id', 'i.customer_group_id', 'i.website_id']
        )->columns(
            [
                'min_price' => new \Zend_Db_Expr('MIN(' . $ifPrice . ')'),
                'max_price' => new \Zend_Db_Expr('SUM(' . $ifPrice . ')'),
            ]
        );

        $query = $select->insertFromSelect($table);
        $connection->query($query);

        $ifTierPrice = $connection->getCheckSql('i.tier_price IS NOT NULL', '(i.tier_price + id.min_price)', 'NULL');

        $select = $connection->select()->join(
            ['id' => $table],
            'i.entity_id = id.entity_id AND i.customer_group_id = id.customer_group_id' .
            ' AND i.website_id = id.website_id',
            []
        )->columns(
            [
                'min_price' => new \Zend_Db_Expr('i.min_price + id.min_price'),
                'max_price' => new \Zend_Db_Expr('i.max_price + id.max_price'),
                'tier_price' => new \Zend_Db_Expr($ifTierPrice),
            ]
        );

        $query = $select->crossUpdateFromSelect(['i' => $this->_getDefaultFinalPriceTable()]);
        $connection->query($query);

        $connection->delete($table);

        return $this;
    }
}
