<?php
/**
 * No route handler interface
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\Router;

interface NoRouteHandlerInterface
{
    /**
     * Check and process no route request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function process(\Magento\Framework\App\RequestInterface $request);
}
