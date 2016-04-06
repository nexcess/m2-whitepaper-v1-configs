<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Module\Output;

interface ConfigInterface
{
    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isEnabled($moduleName);

    /**
     * Retrieve module enabled specific path
     *
     * @param string $path Fully-qualified config path
     * @return boolean
     */
    public function isSetFlag($path);
}
