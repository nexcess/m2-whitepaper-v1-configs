<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Class CronJobException used to check that cron handles execution exception
 * Please see \Magento\Cron\Test\Unit\Model\ObserverTest
 */
namespace Magento\Cron\Test\Unit\Model;

class CronJobException
{
    public function execute()
    {
        throw new \Exception('Test exception');
    }
}
