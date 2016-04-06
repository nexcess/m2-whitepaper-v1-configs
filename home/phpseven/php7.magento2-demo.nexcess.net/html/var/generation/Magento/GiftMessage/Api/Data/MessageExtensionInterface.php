<?php
namespace Magento\GiftMessage\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\GiftMessage\Api\Data\MessageInterface
 */
interface MessageExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return string|null
     */
    public function getEntityId();

    /**
     * @param string $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return string|null
     */
    public function getEntityType();

    /**
     * @param string $entityType
     * @return $this
     */
    public function setEntityType($entityType);
}
