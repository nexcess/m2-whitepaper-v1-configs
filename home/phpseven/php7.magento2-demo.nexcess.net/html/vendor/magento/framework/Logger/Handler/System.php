<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Logger\Handler;

use Magento\Framework\Filesystem\DriverInterface;
use Monolog\Logger;

class System extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/system.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * @var Exception
     */
    protected $exceptionHandler;

    /**
     * @param DriverInterface $filesystem
     * @param Exception $exceptionHandler
     * @param string $filePath
     */
    public function __construct(
        DriverInterface $filesystem,
        Exception $exceptionHandler,
        $filePath = null
    ) {
        $this->exceptionHandler = $exceptionHandler;
        parent::__construct($filesystem, $filePath);
    }

    /**
     * @{inheritDoc}
     *
     * @param $record array
     * @return void
     */
    public function write(array $record)
    {
        if (isset($record['context']['is_exception']) && $record['context']['is_exception']) {
            unset($record['context']['is_exception']);
            $this->exceptionHandler->handle($record);
        } else {
            unset($record['context']['is_exception']);
            $record['formatted'] = $this->getFormatter()->format($record);
            parent::write($record);
        }
    }
}
