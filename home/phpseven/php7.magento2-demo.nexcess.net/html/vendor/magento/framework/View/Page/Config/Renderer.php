<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\View\Page\Config;

use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Page\Config;

/**
 * Page config Renderer model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Renderer implements RendererInterface
{
    /**
     * @var array
     */
    protected $assetTypeOrder = ['css', 'ico', 'js'];

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Asset\MergeService
     */
    protected $assetMergeService;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\View\Asset\MergeService $assetMergeService
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Config $pageConfig,
        \Magento\Framework\View\Asset\MergeService $assetMergeService,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->pageConfig = $pageConfig;
        $this->assetMergeService = $assetMergeService;
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        $this->string = $string;
        $this->logger = $logger;
    }

    /**
     * @param string $elementType
     * @return string
     */
    public function renderElementAttributes($elementType)
    {
        $resultAttributes = [];
        foreach ($this->pageConfig->getElementAttributes($elementType) as $name => $value) {
            $resultAttributes[] = sprintf('%s="%s"', $name, $value);
        }
        return implode(' ', $resultAttributes);
    }

    /**
     * @return string
     */
    public function renderHeadContent()
    {
        $result = '';
        $result .= $this->renderMetadata();
        $result .= $this->renderTitle();
        $this->prepareFavicon();
        $result .= $this->renderAssets($this->getAvailableResultGroups());
        $result .= $this->pageConfig->getIncludes();
        return $result;
    }

    /**
     * @return string
     */
    public function renderTitle()
    {
        return '<title>' . $this->escaper->escapeHtml($this->pageConfig->getTitle()->get()) . '</title>' . "\n";
    }

    /**
     * @return string
     */
    public function renderMetadata()
    {
        $result = '';
        foreach ($this->pageConfig->getMetadata() as $name => $content) {
            $metadataTemplate = $this->getMetadataTemplate($name);
            if (!$metadataTemplate) {
                continue;
            }
            $content = $this->processMetadataContent($name, $content);
            if ($content) {
                $result .= str_replace(['%name', '%content'], [$name, $content], $metadataTemplate);
            }
        }
        return $result;
    }

    /**
     * @param string $name
     * @param string $content
     * @return mixed
     */
    protected function processMetadataContent($name, $content)
    {
        $method = 'get' . $this->string->upperCaseWords($name, '_', '');
        if (method_exists($this->pageConfig, $method)) {
            $content = $this->pageConfig->$method();
        }
        return $content;
    }

    /**
     * @param string $name
     * @return bool|string
     */
    protected function getMetadataTemplate($name)
    {
        switch ($name) {
            case 'charset':
                $metadataTemplate = '<meta charset="%content"/>' . "\n";
                break;

            case 'content_type':
                $metadataTemplate = '<meta http-equiv="Content-Type" content="%content"/>' . "\n";
                break;

            case 'x_ua_compatible':
                $metadataTemplate = '<meta http-equiv="X-UA-Compatible" content="%content"/>' . "\n";
                break;

            case 'media_type':
                $metadataTemplate = false;
                break;

            default:
                $metadataTemplate = '<meta name="%name" content="%content"/>' . "\n";
                break;
        }
        return $metadataTemplate;
    }

    /**
     * @return void
     */
    public function prepareFavicon()
    {
        if ($this->pageConfig->getFaviconFile()) {
            $this->pageConfig->addRemotePageAsset(
                $this->pageConfig->getFaviconFile(),
                Generator\Head::VIRTUAL_CONTENT_TYPE_LINK,
                ['attributes' => ['rel' => 'icon', 'type' => 'image/x-icon']],
                'icon'
            );
            $this->pageConfig->addRemotePageAsset(
                $this->pageConfig->getFaviconFile(),
                Generator\Head::VIRTUAL_CONTENT_TYPE_LINK,
                ['attributes' => ['rel' => 'shortcut icon', 'type' => 'image/x-icon']],
                'shortcut-icon'
            );
        } else {
            $this->pageConfig->addPageAsset(
                $this->pageConfig->getDefaultFavicon(),
                ['attributes' => ['rel' => 'icon', 'type' => 'image/x-icon']],
                'icon'
            );
            $this->pageConfig->addPageAsset(
                $this->pageConfig->getDefaultFavicon(),
                ['attributes' => ['rel' => 'shortcut icon', 'type' => 'image/x-icon']],
                'shortcut-icon'
            );
        }
    }

    /**
     * Returns rendered HTML for all Assets (CSS before)
     *
     * @param array $resultGroups
     *
     * @return string
     */
    public function renderAssets($resultGroups = [])
    {
        /** @var $group \Magento\Framework\View\Asset\PropertyGroup */
        foreach ($this->pageConfig->getAssetCollection()->getGroups() as $group) {
            $type = $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE);
            if (!isset($resultGroups[$type])) {
                $resultGroups[$type] = '';
            }
            $resultGroups[$type] .= $this->renderAssetGroup($group);
        }
        return implode('', $resultGroups);
    }

    /**
     * Returns rendered HTML for an Asset Group
     *
     * @param \Magento\Framework\View\Asset\PropertyGroup $group
     * @return string
     */
    protected function renderAssetGroup(\Magento\Framework\View\Asset\PropertyGroup $group)
    {
        $groupAssets = $this->processMerge($group->getAll(), $group);

        $attributes = $this->getGroupAttributes($group);
        $attributes = $this->addDefaultAttributes(
            $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE),
            $attributes
        );

        $groupTemplate = $this->getAssetTemplate(
            $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE),
            $attributes
        );
        $groupHtml = $this->renderAssetHtml($groupTemplate, $groupAssets);
        $groupHtml = $this->processIeCondition($groupHtml, $group);
        return $groupHtml;
    }

    /**
     * @param array $groupAssets
     * @param \Magento\Framework\View\Asset\PropertyGroup $group
     * @return array
     */
    protected function processMerge($groupAssets, $group)
    {
        if ($group->getProperty(GroupedCollection::PROPERTY_CAN_MERGE) && count($groupAssets) > 1) {
            $groupAssets = $this->assetMergeService->getMergedAssets(
                $groupAssets,
                $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE)
            );
        }
        return $groupAssets;
    }

    /**
     * @param \Magento\Framework\View\Asset\PropertyGroup $group
     * @return string|null
     */
    protected function getGroupAttributes($group)
    {
        $attributes = $group->getProperty('attributes');
        if (!empty($attributes)) {
            if (is_array($attributes)) {
                $attributesString = '';
                foreach ($attributes as $name => $value) {
                    $attributesString .= ' ' . $name . '="' . $this->escaper->escapeHtml($value) . '"';
                }
                $attributes = $attributesString;
            } else {
                $attributes = ' ' . $attributes;
            }
        }
        return $attributes;
    }

    /**
     * @param string $contentType
     * @param string $attributes
     * @return string
     */
    protected function addDefaultAttributes($contentType, $attributes)
    {
        switch ($contentType) {
            case 'js':
                $attributes = ' type="text/javascript" ' . $attributes;
                break;

            case 'css':
                $attributes = ' rel="stylesheet" type="text/css" ' . ($attributes ?: ' media="all"');
                break;
        }
        return $attributes;
    }

    /**
     * @param string $contentType
     * @param string|null $attributes
     * @return string
     */
    protected function getAssetTemplate($contentType, $attributes)
    {
        switch ($contentType) {
            case 'js':
                $groupTemplate = '<script ' . $attributes . ' src="%s"></script>' . "\n";
                break;

            case 'css':
            default:
                $groupTemplate = '<link ' . $attributes . ' href="%s" />' . "\n";
                break;
        }
        return $groupTemplate;
    }

    /**
     * @param string $groupHtml
     * @param \Magento\Framework\View\Asset\PropertyGroup $group
     * @return string
     */
    protected function processIeCondition($groupHtml, $group)
    {
        $ieCondition = $group->getProperty('ie_condition');
        if (!empty($ieCondition)) {
            $groupHtml = '<!--[if ' . $ieCondition . ']>' . "\n" . $groupHtml . '<![endif]-->' . "\n";
        }
        return $groupHtml;
    }

    /**
     * Render HTML tags referencing corresponding URLs
     *
     * @param string $template
     * @param array $assets
     * @return string
     */
    protected function renderAssetHtml($template, $assets)
    {
        $result = '';
        try {
            /** @var $asset \Magento\Framework\View\Asset\AssetInterface */
            foreach ($assets as $asset) {
                $result .= sprintf($template, $asset->getUrl());
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
            $result .= sprintf($template, $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']));
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getAvailableResultGroups()
    {
        return array_fill_keys($this->assetTypeOrder, '');
    }
}
