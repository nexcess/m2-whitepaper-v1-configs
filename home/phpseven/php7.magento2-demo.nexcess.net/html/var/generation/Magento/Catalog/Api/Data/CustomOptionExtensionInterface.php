<?php
namespace Magento\Catalog\Api\Data;

/**
 * ExtensionInterface class for @see
 * \Magento\Catalog\Api\Data\CustomOptionInterface
 */
interface CustomOptionExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return \Magento\Framework\Api\Data\ImageContentInterface|null
     */
    public function getFileInfo();

    /**
     * @param \Magento\Framework\Api\Data\ImageContentInterface $fileInfo
     * @return $this
     */
    public function setFileInfo($fileInfo);
}
