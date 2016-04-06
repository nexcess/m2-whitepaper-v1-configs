<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Checkout\Test\Block\Cart;

use Magento\Customer\Test\Fixture\Address;
use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;

/**
 * Cart shipping block.
 */
class Shipping extends Form
{
    /**
     * Form wrapper selector.
     *
     * @var string
     */
    protected $formWrapper = '.content';

    /**
     * Open shipping form selector.
     *
     * @var string
     */
    protected $openForm = '.title';

    /**
     * Selector to access the shipping carrier method.
     *
     * @var string
     */
    protected $shippingMethod = '//span[text()="%s"]/following::label[contains(., "%s")]/../input';

    /**
     * From with shipping available shipping methods.
     *
     * @var string
     */
    protected $shippingMethodForm = '#co-shipping-method-form';

    /**
     * Fields that are used in estimation shipping form.
     *
     * @var array
     */
    protected $estimationFields = ['country_id', 'region_id', 'region', 'postcode'];

    /**
     * Block wait element.
     *
     * @var string
     */
    protected $blockWaitElement = '._block-content-loading';

    /**
     * Open estimate shipping and tax form.
     *
     * @return void
     */
    public function openEstimateShippingAndTax()
    {
        if (!$this->_rootElement->find($this->formWrapper)->isVisible()) {
            $this->_rootElement->find($this->openForm)->click();
        }
    }

    /**
     * Select shipping method.
     *
     * @param array $shipping
     * @return void
     * @throws \Exception
     */
    public function selectShippingMethod(array $shipping)
    {
        $selector = sprintf($this->shippingMethod, $shipping['shipping_service'], $shipping['shipping_method']);
        if (!$this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible()) {
            $this->openEstimateShippingAndTax();
        }

        $element = $this->_rootElement->find($selector, Locator::SELECTOR_XPATH);
        if (!$element->isDisabled()) {
            $element->click();
        } else {
            throw new \Exception("Unable to set value to field '$selector' as it's disabled.");
        }
    }

    /**
     * Fill shipping and tax form.
     *
     * @param Address $address
     * @return void
     */
    public function fillEstimateShippingAndTax(Address $address)
    {
        $this->openEstimateShippingAndTax();
        $data = $address->getData();
        $mapping = $this->dataMapping(array_intersect_key($data, array_flip($this->estimationFields)));

        // Test environment may become unstable when form fields are filled in a default manner.
        // Imitating behavior closer to the real user.
        foreach ($mapping as $field) {
            $this->_fill([$field], $this->_rootElement);
            $this->waitForUpdatedShippingMethods();
        }
    }

    /**
     * Determines if the specified shipping carrier/method is visible on the cart.
     *
     * @param $carrier
     * @param $method
     * @return bool
     */
    public function isShippingCarrierMethodVisible($carrier, $method)
    {
        $shippingMethodForm = $this->_rootElement->find($this->shippingMethodForm);
        $this->_rootElement->waitUntil(
            function () use ($shippingMethodForm) {
                return $shippingMethodForm->isVisible() ? true : null;
            }
        );
        $selector = sprintf($this->shippingMethod, $carrier, $method);

        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Wait for shipping methods block to update contents asynchronously.
     *
     * @return void
     */
    public function waitForUpdatedShippingMethods()
    {
        // Code under test uses JavaScript delay at this point as well.
        sleep(1);
        $this->waitForElementNotVisible($this->blockWaitElement);
    }
}
