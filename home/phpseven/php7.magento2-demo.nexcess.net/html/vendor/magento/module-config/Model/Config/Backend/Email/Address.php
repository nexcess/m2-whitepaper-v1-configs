<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * System config email field backend model
 */
namespace Magento\Config\Model\Config\Backend\Email;

use Magento\Framework\Exception\LocalizedException;

class Address extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (!\Zend_Validate::is($value, 'EmailAddress')) {
            throw new LocalizedException(__('Please correct the email address: "%1".', $value));
        }
        return $this;
    }
}
