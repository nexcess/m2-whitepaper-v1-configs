<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogRule\Model\Indexer\Rule;

use Magento\CatalogRule\Model\Indexer\AbstractIndexer;

class RuleProductIndexer extends AbstractIndexer
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function doExecuteList($ids)
    {
        $this->indexBuilder->reindexFull();
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function doExecuteRow($id)
    {
        $this->indexBuilder->reindexFull();
    }
}
