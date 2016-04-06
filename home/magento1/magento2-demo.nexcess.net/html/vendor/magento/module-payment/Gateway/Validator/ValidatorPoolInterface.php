<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payment\Gateway\Validator;

use Magento\Framework\Exception\NotFoundException;

interface ValidatorPoolInterface
{
    /**
     * Returns configured validator
     *
     * @param string $code
     * @return \Magento\Payment\Gateway\Validator\ValidatorInterface
     * @throws NotFoundException
     */
    public function get($code);
}
