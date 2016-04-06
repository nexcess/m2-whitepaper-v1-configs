<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\TotalsAdditionalDataInterface
 */
class TotalsAdditionalDataExtension extends \Magento\Framework\Api\AbstractSimpleObject implements \Magento\Quote\Api\Data\TotalsAdditionalDataExtensionInterface
{
    /**
     * @return \Magento\GiftMessage\Api\Data\MessageInterface[]|null
     */
    public function getGiftMessages()
    {
        return $this->_get('gift_messages');
    }

    /**
     * @param \Magento\GiftMessage\Api\Data\MessageInterface[] $giftMessages
     * @return $this
     */
    public function setGiftMessages($giftMessages)
    {
        $this->setData('gift_messages', $giftMessages);
        return $this;
    }
}
