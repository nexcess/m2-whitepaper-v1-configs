<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Model\Payflow\Service\Response\Validator;

use Magento\Framework\DataObject;
use Magento\Paypal\Model\Payflowpro;
use Magento\Framework\Exception\LocalizedException;
use Magento\Paypal\Model\Payflow\Service\Response\ValidatorInterface;

/**
 * Class ResponseValidator
 */
class ResponseValidator implements ValidatorInterface
{
    /**
     * @var ValidatorInterface[]
     */
    protected $validators;

    /**
     * Constructor
     *
     * @param ValidatorInterface[] $validators
     */
    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    /**
     * Validate data
     *
     * @param Object $response
     * @return void
     * @throws LocalizedException
     */
    public function validate(DataObject $response)
    {
        switch ($response->getResult()) {
            case Payflowpro::RESPONSE_CODE_APPROVED:
            case Payflowpro::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                foreach ($this->validators as $validator) {
                    if ($validator->validate($response) === false) {
                        throw new LocalizedException(__('Transaction has been declined'));
                    }
                }
                break;
            case Payflowpro::RESPONSE_CODE_INVALID_AMOUNT:
                break;
            default:
                throw new LocalizedException(__('Transaction has been declined'));
        }
    }
}
