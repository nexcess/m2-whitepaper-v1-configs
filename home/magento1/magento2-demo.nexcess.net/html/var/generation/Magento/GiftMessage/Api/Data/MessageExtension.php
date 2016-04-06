<?php
namespace Magento\GiftMessage\Api\Data;

/**
 * Extension class for @see \Magento\GiftMessage\Api\Data\MessageInterface
 */
class MessageExtension extends \Magento\Framework\Api\AbstractSimpleObject implements \Magento\GiftMessage\Api\Data\MessageExtensionInterface
{
    /**
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->_get('entity_id');
    }

    /**
     * @param string $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        $this->setData('entity_id', $entityId);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEntityType()
    {
        return $this->_get('entity_type');
    }

    /**
     * @param string $entityType
     * @return $this
     */
    public function setEntityType($entityType)
    {
        $this->setData('entity_type', $entityType);
        return $this;
    }
}
