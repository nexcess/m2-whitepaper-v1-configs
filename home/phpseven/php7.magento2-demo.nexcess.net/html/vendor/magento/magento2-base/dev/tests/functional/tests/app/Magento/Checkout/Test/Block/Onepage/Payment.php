<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Checkout\Test\Block\Onepage;

use Magento\Mtf\Block\Block;
use Magento\Payment\Test\Fixture\CreditCard;

/**
 * Checkout payment block.
 */
class Payment extends Block
{
    /**
     * Payment method input selector.
     *
     * @var string
     */
    protected $paymentMethodInput = '#%s';

    /**
     * Labels for payment methods.
     *
     * @var string
     */
    protected $paymentMethodLabels = '.payment-method:not([style="display: none;"]) .payment-method-title label';

    /**
     * Label for payment methods.
     *
     * @var string
     */
    protected $paymentMethodLabel = '[for="%s"]';

    /**
     * Continue checkout button.
     *
     * @var string
     */
    protected $continue = '#payment-buttons-container button';

    /**
     * Place order button.
     *
     * @var string
     */
    protected $placeOrder = '.action.primary.checkout';
    
    /**
     * Wait element.
     *
     * @var string
     */
    protected $waitElement = '.loading-mask';

    /**
     * Purchase order number selector.
     *
     * @var string
     */
    protected $purchaseOrderNumber = '#po_number';

    /**
     * Selector for active payment method.
     *
     * @var string
     */
    protected $activePaymentMethodSelector = '.payment-method._active';


    /**
     * Select payment method.
     *
     * @param array $payment
     * @param CreditCard|null $creditCard
     * @throws \Exception
     * @return void
     */
    public function selectPaymentMethod(array $payment, CreditCard $creditCard = null)
    {
        $paymentSelector = sprintf($this->paymentMethodInput, $payment['method']);
        $paymentLabelSelector = sprintf($this->paymentMethodLabel, $payment['method']);

        try {
            $this->waitForElementVisible($paymentLabelSelector);
        } catch (\Exception $exception) {
            throw new \Exception('Such payment method is absent.');
        }

        $paymentRadioButton = $this->_rootElement->find($paymentSelector);
        if ($paymentRadioButton->isVisible()) {
            $paymentRadioButton->click();
        }

        if ($payment['method'] == "purchaseorder") {
            $this->_rootElement->find($this->purchaseOrderNumber)->setValue($payment['po_number']);
        }
        if ($creditCard !== null) {
            /** @var \Magento\Payment\Test\Block\Form\Cc $formBlock */
            $formBlock = $this->blockFactory->create(
                '\\Magento\\Payment\\Test\\Block\\Form\\Cc',
                ['element' => $this->_rootElement->find('#payment_form_' . $payment['method'])]
            );
            $formBlock->fill($creditCard);
        }
    }

    /**
     * Get selected payment method block.
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Payment\Method
     */
    public function getSelectedPaymentMethodBlock()
    {
        $element = $this->_rootElement->find($this->activePaymentMethodSelector);

        return $this->blockFactory->create(
            '\Magento\Checkout\Test\Block\Onepage\Payment\Method',
            ['element' => $element]
        );
    }

    /**
     * Press "Place Order" button.
     *
     * @return void
     */
    public function placeOrder()
    {
        $this->_rootElement->find($this->placeOrder)->click();
        $this->waitForElementNotVisible($this->waitElement);
    }
}
