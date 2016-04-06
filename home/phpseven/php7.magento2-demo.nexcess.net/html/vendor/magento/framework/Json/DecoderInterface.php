<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Json;

/**
 * JSON decoder
 *
 * @api
 */
interface DecoderInterface
{
    /**
     * Decodes the given $data string which is encoded in the JSON format into a PHP type (array, string literal, etc.)
     *
     * @param string $data
     * @return mixed
     */
    public function decode($data);
}
