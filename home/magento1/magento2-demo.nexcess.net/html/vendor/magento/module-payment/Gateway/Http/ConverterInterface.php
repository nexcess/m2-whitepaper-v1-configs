<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payment\Gateway\Http;

interface ConverterInterface
{
    /**
     * Converts gateway response to ENV structure
     *
     * @param mixed $response
     * @return array
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     */
    public function convert($response);
}
