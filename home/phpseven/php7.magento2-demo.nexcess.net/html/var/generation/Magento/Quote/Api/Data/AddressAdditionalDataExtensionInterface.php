<?php
namespace Magento\Quote\Api\Data;

/**
 * ExtensionInterface class for @see
 * \Magento\Quote\Api\Data\AddressAdditionalDataInterface
 */
interface AddressAdditionalDataExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return string|null
     */
    public function getPersistentRememberMe();

    /**
     * @param string $persistentRememberMe
     * @return $this
     */
    public function setPersistentRememberMe($persistentRememberMe);
}
