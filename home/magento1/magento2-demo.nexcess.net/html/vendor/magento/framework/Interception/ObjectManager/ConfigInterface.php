<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Interception\ObjectManager;

interface ConfigInterface extends \Magento\Framework\ObjectManager\ConfigInterface
{
    /**
     * Set Interception config
     *
     * @param \Magento\Framework\Interception\ConfigInterface $interceptionConfig
     * @return void
     */
    public function setInterceptionConfig(\Magento\Framework\Interception\ConfigInterface $interceptionConfig);

    /**
     * Retrieve instance type without interception processing
     *
     * @param string $instanceName
     * @return string
     */
    public function getOriginalInstanceType($instanceName);
}
