<?php
/**
 * Encapsulates directories structure of a Magento module
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Module;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem;

class Dir
{
    /**#@+
     * Directories within modules
     */
    const MODULE_ETC_DIR = 'etc';
    const MODULE_I18N_DIR = 'i18n';
    const MODULE_VIEW_DIR = 'view';
    const MODULE_CONTROLLER_DIR = 'Controller';
    /**#@-*/

    /**
     * Module registry
     *
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @param ComponentRegistrarInterface $componentRegistrar
     */
    public function __construct(ComponentRegistrarInterface $componentRegistrar)
    {
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * Retrieve full path to a directory of certain type within a module
     *
     * @param string $moduleName Fully-qualified module name
     * @param string $type Type of module's directory to retrieve
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getDir($moduleName, $type = '')
    {
        $path = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);

        if ($type) {
            if (!in_array($type, [
                self::MODULE_ETC_DIR,
                self::MODULE_I18N_DIR,
                self::MODULE_VIEW_DIR,
                self::MODULE_CONTROLLER_DIR
            ])) {
                throw new \InvalidArgumentException("Directory type '{$type}' is not recognized.");
            }
            $path .= '/' . $type;
        }

        return $path;
    }
}
