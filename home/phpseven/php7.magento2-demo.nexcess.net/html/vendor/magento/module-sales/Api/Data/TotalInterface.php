<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TotalInterface
 */
interface TotalInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Base shipping amount.
     */
    const BASE_SHIPPING_AMOUNT = 'base_shipping_amount';
    /*
     * Base shipping canceled.
     */
    const BASE_SHIPPING_CANCELED = 'base_shipping_canceled';
    /*
     * Base shipping invoiced.
     */
    const BASE_SHIPPING_INVOICED = 'base_shipping_invoiced';
    /*
     * Base shipping refunded.
     */
    const BASE_SHIPPING_REFUNDED = 'base_shipping_refunded';
    /*
     * Base shipping tax amount.
     */
    const BASE_SHIPPING_TAX_AMOUNT = 'base_shipping_tax_amount';
    /*
     * Base shipping tax refunded.
     */
    const BASE_SHIPPING_TAX_REFUNDED = 'base_shipping_tax_refunded';
    /*
     * Shipping amount.
     */
    const SHIPPING_AMOUNT = 'shipping_amount';
    /*
     * Shipping canceled.
     */
    const SHIPPING_CANCELED = 'shipping_canceled';
    /*
     * Shipping invoiced.
     */
    const SHIPPING_INVOICED = 'shipping_invoiced';
    /*
     * Shipping refunded.
     */
    const SHIPPING_REFUNDED = 'shipping_refunded';
    /*
     * Shipping tax amount.
     */
    const SHIPPING_TAX_AMOUNT = 'shipping_tax_amount';
    /*
     * Shipping tax refunded.
     */
    const SHIPPING_TAX_REFUNDED = 'shipping_tax_refunded';
    /*
     * Base shipping discount amount.
     */
    const BASE_SHIPPING_DISCOUNT_AMOUNT = 'base_shipping_discount_amount';
    /*
     * Shipping discount amount.
     */
    const SHIPPING_DISCOUNT_AMOUNT = 'shipping_discount_amount';
    /*
     * Shipping discount tax compensation amount.
     */
    const SHIPPING_DISCOUNT_TAX_COMPENSATION_AMOUNT = 'shipping_discount_tax_compensation_amount';
    /*
     * Base shipping discount tax compensation amount.
     */
    const BASE_SHIPPING_DISCOUNT_TAX_COMPENSATION_AMNT = 'base_shipping_discount_tax_compensation_amnt';
    /*
     * Shipping including tax.
     */
    const SHIPPING_INCL_TAX = 'shipping_incl_tax';
    /*
     * Base shipping including tax.
     */
    const BASE_SHIPPING_INCL_TAX = 'base_shipping_incl_tax';
    /**#@-*/

    /**
     * Gets the base shipping amount.
     *
     * @return float|null Base shipping amount.
     */
    public function getBaseShippingAmount();

    /**
     * Gets the base shipping canceled.
     *
     * @return float|null Base shipping canceled.
     */
    public function getBaseShippingCanceled();

    /**
     * Gets the base shipping discount amount.
     *
     * @return float|null Base shipping discount amount.
     */
    public function getBaseShippingDiscountAmount();

    /**
     * Gets the base shipping discount tax compensation amount.
     *
     * @return float Base shipping discount tax compensation amount.
     */
    public function getBaseShippingDiscountTaxCompensationAmnt();

    /**
     * Gets the base shipping including tax.
     *
     * @return float|null Base shipping including tax.
     */
    public function getBaseShippingInclTax();

    /**
     * Gets the base shipping invoiced amount.
     *
     * @return float|null Base shipping invoiced.
     */
    public function getBaseShippingInvoiced();

    /**
     * Gets the base shipping refunded amount.
     *
     * @return float|null Base shipping refunded.
     */
    public function getBaseShippingRefunded();

    /**
     * Gets the base shipping tax amount.
     *
     * @return float|null Base shipping tax amount.
     */
    public function getBaseShippingTaxAmount();

    /**
     * Gets the base shipping tax refunded amount.
     *
     * @return float|null Base shipping tax refunded.
     */
    public function getBaseShippingTaxRefunded();

    /**
     * Gets the shipping amount.
     *
     * @return float|null Shipping amount.
     */
    public function getShippingAmount();

    /**
     * Gets the shipping canceled amount.
     *
     * @return float|null Shipping canceled amount.
     */
    public function getShippingCanceled();

    /**
     * Gets the shipping discount amount.
     *
     * @return float|null Shipping discount amount.
     */
    public function getShippingDiscountAmount();

    /**
     * Gets the shipping discount tax compensation amount.
     *
     * @return float Shipping discount tax compensation amount.
     */
    public function getShippingDiscountTaxCompensationAmount();

    /**
     * Gets the shipping including tax amount.
     *
     * @return float|null Shipping including tax amount.
     */
    public function getShippingInclTax();

    /**
     * Gets the shipping invoiced amount.
     *
     * @return float|null Shipping invoiced amount.
     */
    public function getShippingInvoiced();

    /**
     * Gets the shipping refunded amount.
     *
     * @return float|null Shipping refunded amount.
     */
    public function getShippingRefunded();

    /**
     * Gets the shipping tax amount.
     *
     * @return float|null Shipping tax amount.
     */
    public function getShippingTaxAmount();

    /**
     * Gets the shipping tax refunded amount.
     *
     * @return float|null Shipping tax refunded amount.
     */
    public function getShippingTaxRefunded();

    /**
     * Sets the base shipping amount.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingAmount($amount);

    /**
     * Sets the base shipping canceled.
     *
     * @param float $baseShippingCanceled
     * @return $this
     */
    public function setBaseShippingCanceled($baseShippingCanceled);

    /**
     * Sets the base shipping discount amount.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingDiscountAmount($amount);

    /**
     * Sets the base shipping discount tax compensation amount.
     *
     * @param float $amnt
     * @return $this
     */
    public function setBaseShippingDiscountTaxCompensationAmnt($amnt);

    /**
     * Sets the base shipping including tax.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingInclTax($amount);

    /**
     * Sets the base shipping invoiced amount.
     *
     * @param float $baseShippingInvoiced
     * @return $this
     */
    public function setBaseShippingInvoiced($baseShippingInvoiced);

    /**
     * Sets the base shipping refunded amount.
     *
     * @param float $baseShippingRefunded
     * @return $this
     */
    public function setBaseShippingRefunded($baseShippingRefunded);

    /**
     * Sets the base shipping tax amount.
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingTaxAmount($amount);

    /**
     * Sets the base shipping tax refunded amount.
     *
     * @param float $baseShippingTaxRefunded
     * @return $this
     */
    public function setBaseShippingTaxRefunded($baseShippingTaxRefunded);

    /**
     * Sets the shipping amount.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingAmount($amount);

    /**
     * Sets the shipping canceled amount.
     *
     * @param float $shippingCanceled
     * @return $this
     */
    public function setShippingCanceled($shippingCanceled);

    /**
     * Sets the shipping discount amount.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingDiscountAmount($amount);

    /**
     * Sets the shipping discount tax compensation amount.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingDiscountTaxCompensationAmount($amount);

    /**
     * Sets the shipping including tax amount.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingInclTax($amount);

    /**
     * Sets the shipping invoiced amount.
     *
     * @param float $shippingInvoiced
     * @return $this
     */
    public function setShippingInvoiced($shippingInvoiced);

    /**
     * Sets the shipping refunded amount.
     *
     * @param float $shippingRefunded
     * @return $this
     */
    public function setShippingRefunded($shippingRefunded);

    /**
     * Sets the shipping tax amount.
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingTaxAmount($amount);

    /**
     * Sets the shipping tax refunded amount.
     *
     * @param float $shippingTaxRefunded
     * @return $this
     */
    public function setShippingTaxRefunded($shippingTaxRefunded);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Sales\Api\Data\TotalExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Sales\Api\Data\TotalExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\Sales\Api\Data\TotalExtensionInterface $extensionAttributes
    );
}
