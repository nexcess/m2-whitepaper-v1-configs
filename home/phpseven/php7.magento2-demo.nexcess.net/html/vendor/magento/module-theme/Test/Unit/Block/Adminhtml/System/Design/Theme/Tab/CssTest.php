<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Theme\Test\Unit\Block\Adminhtml\System\Design\Theme\Tab;

class CssTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css
     */
    protected $_model;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlCoder;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->urlBuilder = $this->getMock('Magento\Backend\Model\Url', [], [], '', false);
        $this->urlCoder = $this->getMock('Magento\Framework\Encryption\UrlCoder', [], [], '', false);

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css',
            [
                'formFactory' => $this->getMock('Magento\Framework\Data\FormFactory', [], [], '', false),
                'objectManager' => $this->_objectManager,
                'uploaderService' => $this->getMock(
                        'Magento\Theme\Model\Uploader\Service',
                        [],
                        [],
                        '',
                        false
                    ),
                'urlBuilder' => $this->urlBuilder,
                'urlCoder' => $this->urlCoder
            ]
        );

        $this->_model = $this->getMock(
            'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css',
            ['_getCurrentTheme'],
            $constructArguments,
            '',
            true
        );
    }

    public function testGetUploadCssFileNote()
    {
        $method = self::getMethod('_getUploadCssFileNote');
        /** @var $sizeModel \Magento\Framework\File\Size */
        $sizeModel = $this->getMock('Magento\Framework\File\Size', null, [], '', false);

        $this->_objectManager->expects(
            $this->any()
        )->method(
            'get'
        )->with(
            'Magento\Framework\File\Size'
        )->will(
            $this->returnValue($sizeModel)
        );

        $result = $method->invokeArgs($this->_model, []);
        $expectedResult = 'Allowed file types *.css.<br />';
        $expectedResult .= 'This file will replace the current custom.css file and can\'t be more than 2 MB.<br />';
        $expectedResult .= sprintf('Max file size to upload %sM', $sizeModel->getMaxFileSizeInMb());
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetAdditionalElementTypes()
    {
        $method = self::getMethod('_getAdditionalElementTypes');

        /** @var $configModel \Magento\Framework\App\Config\ScopeConfigInterface */
        $configModel = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $this->_objectManager->expects(
            $this->any()
        )->method(
            'get'
        )->with(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        )->will(
            $this->returnValue($configModel)
        );

        $result = $method->invokeArgs($this->_model, []);
        $expectedResult = [
            'links' => 'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element\Links',
            'css_file' => 'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element\File',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTabLabel()
    {
        $this->assertEquals('CSS Editor', $this->_model->getTabLabel());
    }

    /**
     * @param string $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * cover \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css::getDownloadUrl
     */
    public function testGetterDownloadUrl()
    {
        $fileId = 1;
        $themeId = 1;
        $this->urlCoder->expects($this->atLeastOnce())->method('encode')->with($fileId)
            ->will($this->returnValue('encoded'));
        $this->urlBuilder->expects($this->atLeastOnce())->method('getUrl')
            ->with($this->anything(), ['theme_id' => $themeId, 'file' => 'encoded']);
        $this->_model->getDownloadUrl($fileId, $themeId);
    }
}
