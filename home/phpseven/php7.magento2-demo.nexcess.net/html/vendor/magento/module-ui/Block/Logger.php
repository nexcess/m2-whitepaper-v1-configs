<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Block;

use Magento\Framework\View\Element\Template;
use Magento\Ui\Model\Config;

class Logger extends Template
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Is session storage logging enabled
     *
     * @return bool
     */
    public function isLoggingEnabled()
    {
        return $this->config->isLoggingEnabled();
    }

    /**
     * Get session storage key
     *
     * @return string
     */
    public function getSessionStorageKey()
    {
        return $this->config->getSessionStorageKey();
    }
}
