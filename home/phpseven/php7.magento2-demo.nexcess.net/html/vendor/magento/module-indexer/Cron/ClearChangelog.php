<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Indexer\Cron;

class ClearChangelog
{
    /**
     * @var \Magento\Indexer\Model\Processor
     */
    protected $processor;

    /**
     * @param \Magento\Indexer\Model\Processor $processor
     */
    public function __construct(
        \Magento\Indexer\Model\Processor $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * Clean indexer view changelogs
     *
     * @return void
     */
    public function execute()
    {
        $this->processor->clearChangelog();
    }
}
