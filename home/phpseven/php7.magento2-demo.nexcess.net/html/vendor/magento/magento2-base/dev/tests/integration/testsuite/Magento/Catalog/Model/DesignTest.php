<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Catalog\Model\Design.
 */
namespace Magento\Catalog\Model;

class DesignTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Design
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Design'
        );
    }

    /**
     * @dataProvider getThemeModel
     */
    public function testApplyCustomDesign($theme)
    {
        $this->_model->applyCustomDesign($theme);
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\DesignInterface'
        );
        $this->assertEquals('package', $design->getDesignTheme()->getPackageCode());
        $this->assertEquals('theme', $design->getDesignTheme()->getThemeCode());
    }

    /**
     * @return \Magento\Theme\Model\Theme
     */
    public function getThemeModel()
    {
        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\View\Design\ThemeInterface'
        );
        $theme->setData($this->_getThemeData());
        return [[$theme]];
    }

    /**
     * @return array
     */
    protected function _getThemeData()
    {
        return [
            'theme_title' => 'Magento Theme',
            'theme_code' => 'theme',
            'package_code' => 'package',
            'theme_path' => 'package/theme',
            'parent_theme' => null,
            'is_featured' => true,
            'preview_image' => '',
            'theme_directory' => __DIR__ . '_files/design/frontend/default/default'
        ];
    }
}
