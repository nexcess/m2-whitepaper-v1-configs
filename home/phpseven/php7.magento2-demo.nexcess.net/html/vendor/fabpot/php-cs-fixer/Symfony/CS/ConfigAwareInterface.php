<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface ConfigAwareInterface
{
    /**
     * Sets the active config on the fixer.
     *
     * @param ConfigInterface $config
     */
    public function setConfig(ConfigInterface $config);
}
