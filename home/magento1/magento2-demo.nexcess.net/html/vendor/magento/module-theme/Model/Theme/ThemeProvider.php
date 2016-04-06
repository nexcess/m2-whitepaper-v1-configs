<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Model\Theme;

class ThemeProvider implements \Magento\Framework\View\Design\Theme\ThemeProviderInterface
{
    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Theme\Model\ThemeFactory
     */
    protected $themeFactory;

    /**
     * @param \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $collectionFactory
     * @param \Magento\Theme\Model\ThemeFactory $themeFactory
     */
    public function __construct(
        \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $collectionFactory,
        \Magento\Theme\Model\ThemeFactory $themeFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->themeFactory = $themeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeByFullPath($fullPath)
    {
        /** @var $themeCollection \Magento\Theme\Model\ResourceModel\Theme\Collection */
        $themeCollection = $this->collectionFactory->create();
        return $themeCollection->getThemeByFullPath($fullPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeCustomizations(
        $area = \Magento\Framework\App\Area::AREA_FRONTEND,
        $type = \Magento\Framework\View\Design\ThemeInterface::TYPE_VIRTUAL
    ) {
        /** @var $themeCollection \Magento\Theme\Model\ResourceModel\Theme\Collection */
        $themeCollection = $this->collectionFactory->create();
        $themeCollection->addAreaFilter($area)->addTypeFilter($type);
        return $themeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeById($themeId)
    {
        /** @var $themeModel \Magento\Framework\View\Design\ThemeInterface */
        $themeModel = $this->themeFactory->create();
        return $themeModel->load($themeId);
    }
}
