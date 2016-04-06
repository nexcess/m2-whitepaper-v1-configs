<?php
namespace Magento\Quote\Api\Data;

/**
 * ExtensionInterface class for @see
 * \Magento\Quote\Api\Data\TotalsAdditionalDataInterface
 */
interface TotalsAdditionalDataExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return \Magento\GiftMessage\Api\Data\MessageInterface[]|null
     */
    public function getGiftMessages();

    /**
     * @param \Magento\GiftMessage\Api\Data\MessageInterface[] $giftMessages
     * @return $this
     */
    public function setGiftMessages($giftMessages);
}
