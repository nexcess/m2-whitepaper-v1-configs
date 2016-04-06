<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Order actions block.
 */
class Actions extends Block
{
    /**
     * 'Back' button.
     *
     * @var string
     */
    protected $back = '#back';

    /**
     * 'Edit' button.
     *
     * @var string
     */
    protected $edit = '#order_edit';

    /**
     * 'Cancel' button.
     *
     * @var string
     */
    protected $cancel = '[id$=cancel-button]';

    /**
     * 'Send Email' button.
     *
     * @var string
     */
    protected $sendEmail = '#send_notification';

    /**
     * 'Void' button.
     *
     * @var string
     */
    protected $void = '#void_payment';

    /**
     * 'Hold' button.
     *
     * @var string
     */
    protected $hold = '[id$=hold-button]';

    /**
     * 'Invoice' button.
     *
     * @var string
     */
    protected $invoice = '#order_invoice';

    /**
     * 'Reorder' button.
     *
     * @var string
     */
    protected $reorder = '#order_reorder';

    /**
     * 'Ship' button.
     *
     * @var string
     */
    protected $ship = '#order_ship';

    /**
     * 'Credit Memo' button on the order page.
     *
     * @var string
     */
    protected $orderCreditMemo = '#order_creditmemo';

    /**
     * 'Credit Memo' button on the order invoice page.
     *
     * @var string
     */
    protected $orderInvoiceCreditMemo = '#capture';

    /**
     * 'Refund' button.
     *
     * @var string
     */
    protected $refund = '.submit-button.refund';

    /**
     * 'Refund Offline' button.
     *
     * @var string
     */
    protected $refundOffline = '.submit-button';

    /**
     * General button selector.
     *
     * @var string
     */
    protected $button = '//button[@title="%s"]';

    /**
     * Selector for confirm.
     *
     * @var string
     */
    protected $confirmModal = '.confirm._show[data-role=modal]';

    /**
     * Ship order.
     *
     * @return void
     */
    public function ship()
    {
        $this->_rootElement->find($this->ship)->click();
    }

    /**
     * Invoice order.
     *
     * @return void
     */
    public function invoice()
    {
        $this->_rootElement->find($this->invoice)->click();
    }

    /**
     * Reorder order.
     *
     * @return void
     */
    public function reorder()
    {
        $this->_rootElement->find($this->reorder)->click();
    }

    /**
     * Go back.
     *
     * @return void
     */
    public function back()
    {
        $this->_rootElement->find($this->back)->click();
    }

    /**
     * Edit order.
     *
     * @return void
     */
    public function edit()
    {
        $this->_rootElement->find($this->edit)->click();
    }

    /**
     * Cancel order.
     *
     * @return void
     */
    public function cancel()
    {
        $this->_rootElement->find($this->cancel)->click();
        $element = $this->browser->find($this->confirmModal);
        /** @var \Magento\Ui\Test\Block\Adminhtml\Modal $modal */
        $modal = $this->blockFactory->create('Magento\Ui\Test\Block\Adminhtml\Modal', ['element' => $element]);
        $modal->acceptAlert();
    }

    /**
     * Send email.
     *
     * @return void
     */
    public function sendEmail()
    {
        $this->_rootElement->find($this->sendEmail)->click();
    }

    /**
     * Void order.
     *
     * @return void
     */
    public function void()
    {
        $this->_rootElement->find($this->void)->click();
    }

    /**
     * Hold order.
     *
     * @return void
     */
    public function hold()
    {
        $this->_rootElement->find($this->hold)->click();
    }

    /**
     * Order credit memo.
     *
     * @return void
     */
    public function orderCreditMemo()
    {
        $this->_rootElement->find($this->orderCreditMemo)->click();
    }

    /**
     * Order invoice credit memo.
     *
     * @return void
     */
    public function orderInvoiceCreditMemo()
    {
        $this->_rootElement->find($this->orderInvoiceCreditMemo)->click();
    }

    /**
     * Refund order.
     *
     * @return void
     */
    public function refund()
    {
        $this->_rootElement->find($this->refund, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Refund offline order.
     *
     * @return void
     */
    public function refundOffline()
    {
        $this->_rootElement->find($this->refundOffline, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Check if action button is visible.
     *
     * @param string $buttonName
     * @return bool
     */
    public function isActionButtonVisible($buttonName)
    {
        return $this->_rootElement->find(sprintf($this->button, $buttonName), Locator::SELECTOR_XPATH)->isVisible();
    }
}
