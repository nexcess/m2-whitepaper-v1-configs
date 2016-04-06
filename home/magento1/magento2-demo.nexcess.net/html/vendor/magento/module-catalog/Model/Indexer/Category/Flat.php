<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Indexer\Category;

class Flat implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Action\FullFactory
     */
    protected $fullActionFactory;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Action\RowsFactory
     */
    protected $rowsActionFactory;

    /** @var \Magento\Framework\Indexer\IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @param Flat\Action\FullFactory $fullActionFactory
     * @param Flat\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        Flat\Action\FullFactory $fullActionFactory,
        Flat\Action\RowsFactory $rowsActionFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->fullActionFactory = $fullActionFactory;
        $this->rowsActionFactory = $rowsActionFactory;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $indexer = $this->indexerRegistry->get(Flat\State::INDEXER_ID);
        if ($indexer->isInvalid()) {
            return;
        }

        /** @var Flat\Action\Rows $action */
        $action = $this->rowsActionFactory->create();
        if ($indexer->isWorking()) {
            $action->reindex($ids, true);
        }
        $action->reindex($ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->fullActionFactory->create()->reindexAll();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }
}
