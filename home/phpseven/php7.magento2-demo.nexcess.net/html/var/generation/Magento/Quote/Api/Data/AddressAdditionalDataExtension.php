<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\AddressAdditionalDataInterface
 */
class AddressAdditionalDataExtension extends \Magento\Framework\Api\AbstractSimpleObject implements \Magento\Quote\Api\Data\AddressAdditionalDataExtensionInterface
{
    /**
     * @return string|null
     */
    public function getPersistentRememberMe()
    {
        return $this->_get('persistent_remember_me');
    }

    /**
     * @param string $persistentRememberMe
     * @return $this
     */
    public function setPersistentRememberMe($persistentRememberMe)
    {
        $this->setData('persistent_remember_me', $persistentRememberMe);
        return $this;
    }
}
