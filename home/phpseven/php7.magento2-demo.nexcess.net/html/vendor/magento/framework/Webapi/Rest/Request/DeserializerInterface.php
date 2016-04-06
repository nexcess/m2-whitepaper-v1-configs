<?php
/**
 * Interface of REST request content deserializer.
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Webapi\Rest\Request;

interface DeserializerInterface
{
    /**
     * Parse request body into array of params.
     *
     * @param string $body Posted content from request
     * @return array|null Return NULL if content is invalid
     */
    public function deserialize($body);
}
