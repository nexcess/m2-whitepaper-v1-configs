<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Block\Form;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Class Register
 * Register new customer on Frontend
 */
class Register extends Form
{
    /**
     * 'Submit' form button
     *
     * @var string
     */
    protected $submit = '.action.submit';

    /**
     * Locator for customer attribute on New Order page
     *
     * @var string
     */
    protected $customerAttribute = "[name='%s']";

    /**
     * Create new customer account and fill billing address if it exists
     *
     * @param FixtureInterface $fixture
     * @param $address
     */
    public function registerCustomer(FixtureInterface $fixture, $address = null)
    {
        $this->fill($fixture);
        if ($address !== null) {
            $this->fill($address);
        }
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
    }
}
