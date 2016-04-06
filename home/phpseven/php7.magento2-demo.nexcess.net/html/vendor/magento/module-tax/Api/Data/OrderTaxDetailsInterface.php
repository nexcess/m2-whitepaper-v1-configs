<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Api\Data;

interface OrderTaxDetailsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get applied taxes at order level
     *
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface[] | null
     */
    public function getAppliedTaxes();

    /**
     * Set applied taxes at order level
     *
     * @param \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface[] $appliedTaxes
     * @return $this
     */
    public function setAppliedTaxes(array $appliedTaxes = null);

    /**
     * Get order item tax details
     *
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsItemInterface[] | null
     */
    public function getItems();

    /**
     * Set order item tax details
     *
     * @param \Magento\Tax\Api\Data\OrderTaxDetailsItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Tax\Api\Data\OrderTaxDetailsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\Tax\Api\Data\OrderTaxDetailsExtensionInterface $extensionAttributes
    );
}
