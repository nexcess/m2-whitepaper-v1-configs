<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Framework\View\Page\Config
 */
namespace Magento\Framework\View\Test\Unit\Page;

use Magento\Framework\Locale\Resolver;
use Magento\Framework\View\Page\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $model;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\View\Asset\GroupedCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageAssets;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\View\Page\FaviconInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $favicon;

    /**
     * @var \Magento\Framework\View\Layout\BuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $builder;

    /**
     * @var \Magento\Framework\View\Asset\File|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $asset;

    /**
     * @var \Magento\Framework\View\Asset\Remote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $remoteAsset;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $title;

    public function setUp()
    {
        $this->assetRepo = $this->getMock('Magento\Framework\View\Asset\Repository', [], [], '', false);
        $this->pageAssets = $this->getMock('Magento\Framework\View\Asset\GroupedCollection', [], [], '', false);
        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);
        $this->favicon = $this->getMock('Magento\Framework\View\Page\FaviconInterface', [], [], '', false);
        $this->builder = $this->getMock('Magento\Framework\View\Layout\BuilderInterface', [], [], '', false);
        $this->asset = $this->getMock('Magento\Framework\View\Asset\File', [], [], '', false);
        $this->remoteAsset = $this->getMock('\Magento\Framework\View\Asset\Remote', [], [], '', false);
        $this->title = $this->getMock('Magento\Framework\View\Page\Title', [], [], '', false);
        $locale = $this->getMockForAbstractClass('Magento\Framework\Locale\ResolverInterface', [], '', false);
        $locale->expects($this->any())
            ->method('getLocale')
            ->willReturn(Resolver::DEFAULT_LOCALE);
        $this->model = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject(
                'Magento\Framework\View\Page\Config',
                [
                    'assetRepo' => $this->assetRepo,
                    'pageAssets' => $this->pageAssets,
                    'scopeConfig' => $this->scopeConfig,
                    'favicon' => $this->favicon,
                    'localeResolver' => $locale,
                ]
            );
    }

    public function testSetBuilder()
    {
        $this->assertInstanceOf(
            'Magento\Framework\View\Page\Config',
            $this->model->setBuilder($this->builder)
        );
    }

    public function testBuild()
    {
        $this->model->setBuilder($this->builder);
        $this->builder->expects($this->once())->method('build')->will(
            $this->returnValue('Magento\Framework\View\LayoutInterface')
        );
        $this->model->publicBuild();
    }

    public function testGetTitle()
    {
        $this->assertInstanceOf('\Magento\Framework\View\Page\Title', $this->model->getTitle());
    }

    public function testMetadata()
    {
        $expectedMetadata = [
            'charset' => null,
            'media_type' => null,
            'content_type' => null,
            'description' => null,
            'keywords' => null,
            'robots' => null,
            'name' => 'test_value',
        ];
        $this->model->setMetadata('name', 'test_value');
        $this->assertEquals($expectedMetadata, $this->model->getMetadata());
    }

    public function testContentType()
    {
        $contentType = 'test_content_type';
        $this->model->setContentType($contentType);
        $this->assertEquals($contentType, $this->model->getContentType());
    }

    public function testContentTypeEmpty()
    {
        $expectedData = 'default_media_type; charset=default_charset';
        $this->scopeConfig->expects($this->at(0))->method('getValue')->with('design/head/default_media_type', 'store')
            ->will($this->returnValue('default_media_type'));
        $this->scopeConfig->expects($this->at(1))->method('getValue')->with('design/head/default_charset', 'store')
            ->will($this->returnValue('default_charset'));
        $this->assertEquals($expectedData, $this->model->getContentType());
    }

    public function testMediaType()
    {
        $mediaType = 'test_media_type';
        $this->model->setMediaType($mediaType);
        $this->assertEquals($mediaType, $this->model->getMediaType());
    }

    public function testMediaTypeEmpty()
    {
        $expectedData = 'default_media_type';
        $this->scopeConfig->expects($this->once())->method('getValue')->with('design/head/default_media_type', 'store')
            ->will($this->returnValue('default_media_type'));
        $this->assertEquals($expectedData, $this->model->getMediaType());
    }

    public function testCharset()
    {
        $charset = 'test_charset';
        $this->model->setCharset($charset);
        $this->assertEquals($charset, $this->model->getCharset());
    }

    public function testCharsetEmpty()
    {
        $expectedData = 'default_charset';
        $this->scopeConfig->expects($this->once())->method('getValue')->with('design/head/default_charset', 'store')
            ->will($this->returnValue('default_charset'));
        $this->assertEquals($expectedData, $this->model->getCharset());
    }

    public function testDescription()
    {
        $description = 'test_description';
        $this->model->setDescription($description);
        $this->assertEquals($description, $this->model->getDescription());
    }

    public function testDescriptionEmpty()
    {
        $expectedData = 'default_description';
        $this->scopeConfig->expects($this->once())->method('getValue')->with('design/head/default_description', 'store')
            ->will($this->returnValue('default_description'));
        $this->assertEquals($expectedData, $this->model->getDescription());
    }

    public function testKeywords()
    {
        $keywords = 'test_keywords';
        $this->model->setKeywords($keywords);
        $this->assertEquals($keywords, $this->model->getKeywords());
    }

    public function testKeywordsEmpty()
    {
        $expectedData = 'default_keywords';
        $this->scopeConfig->expects($this->once())->method('getValue')->with('design/head/default_keywords', 'store')
            ->will($this->returnValue('default_keywords'));
        $this->assertEquals($expectedData, $this->model->getKeywords());
    }

    public function testRobots()
    {
        $robots = 'test_robots';
        $this->model->setRobots($robots);
        $this->assertEquals($robots, $this->model->getRobots());
    }

    public function testRobotsEmpty()
    {
        $expectedData = 'default_robots';
        $this->scopeConfig->expects($this->once())->method('getValue')->with(
            'design/search_engine_robots/default_robots',
            'store'
        )
            ->will($this->returnValue('default_robots'));
        $this->assertEquals($expectedData, $this->model->getRobots());
    }

    public function testGetAssetCollection()
    {
        $this->assertInstanceOf('Magento\Framework\View\Asset\GroupedCollection', $this->model->getAssetCollection());
    }

    /**
     * @param string $file
     * @param array $properties
     * @param string|null $name
     * @param string $expectedName
     *
     * @dataProvider pageAssetDataProvider
     */
    public function testAddPageAsset($file, $properties, $name, $expectedName)
    {
        $this->assetRepo->expects($this->once())->method('createAsset')->with($file)->will(
            $this->returnValue($this->asset)
        );
        $this->pageAssets->expects($this->once())->method('add')->with($expectedName, $this->asset, $properties);
        $this->assertInstanceOf(
            'Magento\Framework\View\Page\Config',
            $this->model->addPageAsset($file, $properties, $name)
        );
    }

    public function pageAssetDataProvider()
    {
        return [
            [
                'test.php',
                ['one', 'two', 3],
                'test_name',
                'test_name',
            ],
            [
                'filename',
                [],
                null,
                'filename'
            ]
        ];
    }

    /**
     * @param string $url
     * @param string $contentType
     * @param array $properties
     * @param string|null $name
     * @param string $expectedName
     *
     * @dataProvider remotePageAssetDataProvider
     */
    public function testAddRemotePageAsset($url, $contentType, $properties, $name, $expectedName)
    {
        $this->assetRepo->expects($this->once())->method('createRemoteAsset')->with($url, $contentType)->will(
            $this->returnValue($this->remoteAsset)
        );
        $this->pageAssets->expects($this->once())->method('add')->with($expectedName, $this->remoteAsset, $properties);
        $this->assertInstanceOf(
            'Magento\Framework\View\Page\Config',
            $this->model->addRemotePageAsset($url, $contentType, $properties, $name)
        );
    }

    public function remotePageAssetDataProvider()
    {
        return [
            [
                'http://test.com',
                '<body><context>some content</context></body>',
                ['one', 'two', 3],
                'test_name',
                'test_name',
            ],
            [
                'http://test.com',
                '',
                [],
                null,
                'http://test.com'
            ]
        ];
    }

    public function testAddRss()
    {
        $title = 'test title';
        $href = 'http://test.com';
        $expected = ['attributes' => 'rel="alternate" type="application/rss+xml" title="test title"'];
        $this->assetRepo->expects($this->once())->method('createRemoteAsset')->with($href, 'unknown')->will(
            $this->returnValue($this->remoteAsset)
        );
        $this->pageAssets->expects($this->once())->method('add')->with(
            'link/http://test.com',
            $this->remoteAsset,
            $expected
        );
        $this->assertInstanceOf('Magento\Framework\View\Page\Config', $this->model->addRss($title, $href));
    }

    public function testAddBodyClass()
    {
        $className = 'test class';
        $this->assertInstanceOf('Magento\Framework\View\Page\Config', $this->model->addBodyClass($className));
        $this->assertEquals('test-class', $this->model->getElementAttribute('body', 'class'));
    }

    /**
     * @param string $elementType
     * @param string $attribute
     * @param string $value
     *
     * @dataProvider elementAttributeDataProvider
     */
    public function testElementAttribute($elementType, $attribute, $value)
    {
        $this->model->setElementAttribute($elementType, $attribute, $value);
        $this->assertEquals($value, $this->model->getElementAttribute($elementType, $attribute));
    }

    public function elementAttributeDataProvider()
    {
        return [
            [
                'head',
                'class',
                'test',
            ],
            [
                'body',
                'class',
                'value'
            ],
            [
                Config::ELEMENT_TYPE_HTML,
                Config::HTML_ATTRIBUTE_LANG,
                str_replace('_', '-', Resolver::DEFAULT_LOCALE)
            ],
        ];
    }

    /**
     * @param string $elementType
     * @param string $attribute
     * @param string $value
     *
     * @dataProvider elementAttributeExceptionDataProvider
     */
    public function testElementAttributeException($elementType, $attribute, $value)
    {
        $this->setExpectedException('\Magento\Framework\Exception\LocalizedException', $elementType . " isn't allowed");
        $this->model->setElementAttribute($elementType, $attribute, $value);
    }

    public function elementAttributeExceptionDataProvider()
    {
        return [
            [
                'test',
                'class',
                'test',
            ],
            [
                '',
                '',
                ''
            ],
            [
                null,
                null,
                null
            ]
        ];
    }

    /**
     * @param string $elementType
     * @param string $attributes
     *
     * @dataProvider elementAttributesDataProvider
     */
    public function testElementAttributes($elementType, $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->model->setElementAttribute($elementType, $attribute, $value);
        }
        $this->assertEquals($attributes, $this->model->getElementAttributes($elementType));
    }

    public function elementAttributesDataProvider()
    {
        return [
            [
                'html',
                [
                    'context' => 'value',
                    Config::HTML_ATTRIBUTE_LANG => str_replace('_', '-', Resolver::DEFAULT_LOCALE)
                ],
            ],
        ];
    }

    /**
     * @param string $handle
     *
     * @dataProvider pageLayoutDataProvider
     */
    public function testPageLayout($handle)
    {
        $this->model->setPageLayout($handle);
        $this->assertEquals($handle, $this->model->getPageLayout());
    }

    public function pageLayoutDataProvider()
    {
        return [
            [
                'test',
            ],
            [
                ''
            ],
            [
                null
            ],
            [
                [
                    'test',
                ]
            ]
        ];
    }

    public function testGetFaviconFile()
    {
        $expected = 'test';
        $this->favicon->expects($this->once())->method('getFaviconFile')->will($this->returnValue($expected));
        $this->assertEquals($expected, $this->model->getFaviconFile());
    }

    public function testGetDefaultFavicon()
    {
        $this->favicon->expects($this->once())->method('getDefaultFavicon');
        $this->model->getDefaultFavicon();
    }

    public function testGetIncludes()
    {
        $xml = '
            <script type="text/javascript">
                Fieldset.addToPrefix(1);
            </script>
            ';
        $this->scopeConfig->expects($this->once())->method('getValue')->with('design/head/includes', 'store')->will(
            $this->returnValue($xml)
        );
        $this->assertEquals($xml, $this->model->getIncludes());
    }
}
