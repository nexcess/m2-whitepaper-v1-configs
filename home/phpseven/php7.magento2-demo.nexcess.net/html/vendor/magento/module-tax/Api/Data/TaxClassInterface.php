<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Api\Data;

/**
 * Tax class interface.
 * @api
 */
interface TaxClassInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get tax class ID.
     *
     * @return int|null
     */
    public function getClassId();

    /**
     * Set tax class ID.
     *
     * @param int $classId
     * @return $this
     */
    public function setClassId($classId);

    /**
     * Get tax class name.
     *
     * @return string
     */
    public function getClassName();

    /**
     * Set tax class name.
     *
     * @param string $className
     * @return $this
     */
    public function setClassName($className);

    /**
     * Get tax class type.
     *
     * @return string
     */
    public function getClassType();

    /**
     * Set tax class type.
     *
     * @param string $classType
     * @return $this
     */
    public function setClassType($classType);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Tax\Api\Data\TaxClassExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Tax\Api\Data\TaxClassExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magento\Tax\Api\Data\TaxClassExtensionInterface $extensionAttributes);
}
