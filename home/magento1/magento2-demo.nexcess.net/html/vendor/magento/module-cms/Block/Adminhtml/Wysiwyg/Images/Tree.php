<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cms\Block\Adminhtml\Wysiwyg\Images;

/**
 * Directory tree renderer for Cms Wysiwyg Images
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Tree extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Cms wysiwyg images
     *
     * @var \Magento\Cms\Helper\Wysiwyg\Images
     */
    protected $_cmsWysiwygImages = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Cms\Helper\Wysiwyg\Images $cmsWysiwygImages
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Helper\Wysiwyg\Images $cmsWysiwygImages,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_cmsWysiwygImages = $cmsWysiwygImages;
        parent::__construct($context, $data);
    }

    /**
     * Json tree builder
     *
     * @return string
     */
    public function getTreeJson()
    {
        $storageRoot = $this->_cmsWysiwygImages->getStorageRoot();
        $collection = $this->_coreRegistry->registry(
            'storage'
        )->getDirsCollection(
            $this->_cmsWysiwygImages->getCurrentPath()
        );
        $jsonArray = [];
        foreach ($collection as $item) {
            $jsonArray[] = [
                'text' => $this->_cmsWysiwygImages->getShortFilename($item->getBasename(), 20),
                'id' => $this->_cmsWysiwygImages->convertPathToId($item->getFilename()),
                'path' => substr($item->getFilename(), strlen($storageRoot)),
                'cls' => 'folder',
            ];
        }
        return \Zend_Json::encode($jsonArray);
    }

    /**
     * Json source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        return $this->getUrl('cms/*/treeJson');
    }

    /**
     * Root node name of tree
     *
     * @return \Magento\Framework\Phrase
     */
    public function getRootNodeName()
    {
        return __('Storage Root');
    }

    /**
     * Return tree node full path based on current path
     *
     * @return string
     */
    public function getTreeCurrentPath()
    {
        $treePath = ['root'];
        if ($path = $this->_coreRegistry->registry('storage')->getSession()->getCurrentPath()) {
            $path = str_replace($this->_cmsWysiwygImages->getStorageRoot(), '', $path);
            $relative = [];
            foreach (explode('/', $path) as $dirName) {
                if ($dirName) {
                    $relative[] = $dirName;
                    $treePath[] = $this->_cmsWysiwygImages->idEncode(implode('/', $relative));
                }
            }
        }
        return $treePath;
    }

    /**
     * Get tree widget options
     *
     * @return array
     */
    public function getTreeWidgetOptions()
    {
        return [
            "folderTree" => [
                "rootName" => $this->getRootNodeName(),
                "url" => $this->getTreeLoaderUrl(),
                "currentPath" => array_reverse($this->getTreeCurrentPath()),
            ]
        ];
    }
}
