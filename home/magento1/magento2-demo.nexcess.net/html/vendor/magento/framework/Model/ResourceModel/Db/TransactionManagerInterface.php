<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\ResourceModel\Db;

use Magento\Framework\DB\Adapter\AdapterInterface as Connection;

interface TransactionManagerInterface
{
    /**
     * Start transaction
     *
     * @param Connection $connection
     * @return Connection
     */
    public function start(Connection $connection);

    /**
     * Commit transaction
     *
     * @return void
     */
    public function commit();

    /**
     * Rollback transaction
     *
     * @return void
     */
    public function rollBack();
}
