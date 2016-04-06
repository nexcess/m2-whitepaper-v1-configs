<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payment\Gateway\Helper;

use Magento\Payment\Model\InfoInterface;

class ContextHelper
{
    /**
     * Asserts is an Order payment
     *
     * @param InfoInterface $paymentInfo
     * @throws \LogicException
     * @return null
     */
    public static function assertOrderPayment(InfoInterface $paymentInfo)
    {
        if (!$paymentInfo instanceof \Magento\Sales\Model\Order\Payment) {
            throw new \LogicException('Order payment should be provided.');
        }
    }

    /**
     * Asserts is an Quote payment
     *
     * @param InfoInterface $paymentInfo
     * @throws \LogicException
     * @return null
     */
    public static function assertQuotePayment(InfoInterface $paymentInfo)
    {
        if (!$paymentInfo instanceof \Magento\Quote\Model\Quote\Payment) {
            throw new \LogicException('Quote payment should be provided.');
        }
    }
}
