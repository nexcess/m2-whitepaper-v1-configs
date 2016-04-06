<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Block\Address;

use Magento\Customer\Test\Fixture\Address;
use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;

/**
 * Class Edit
 * Customer address edit block
 */
class Edit extends Form
{
    /**
     * 'Save address' button
     *
     * @var string
     */
    protected $saveAddress = '[data-action=save-address]';

    /**
     * VAT field selector
     *
     * @var string
     */
    protected $vatFieldId = 'vat_id';

    /**
     * Edit customer address
     *
     * @param Address $fixture
     */
    public function editCustomerAddress(Address $fixture)
    {
        $this->fill($fixture);
        $this->saveAddress();
    }

    /**
     * Save new VAT id
     *
     * @param $vat
     */
    public function saveVatID($vat)
    {
        $this->_rootElement->find($this->vatFieldId, Locator::SELECTOR_ID)->setValue($vat);
        $this->saveAddress();
    }

    /**
     * Click on save address button
     *
     * @return void
     */
    public function saveAddress()
    {
        $this->_rootElement->find($this->saveAddress)->click();
    }
}
