<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Swatches\Test\Unit\Block\Product\Renderer;

use Magento\Swatches\Block\Product\Renderer\Configurable;
use Magento\Swatches\Model\Swatch;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigurableTest extends \PHPUnit_Framework_TestCase
{
    /** @var Configurable */
    private $configurable;

    /** @var \Magento\Framework\Stdlib\ArrayUtils|\PHPUnit_Framework_MockObject_MockObject */
    private $arrayUtils;

    /** @var \Magento\Framework\Json\EncoderInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $jsonEncoder;

    /** @var \Magento\ConfigurableProduct\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    private $helper;

    /** @var \Magento\Swatches\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    private $swatchHelper;

    /** @var \Magento\Swatches\Helper\Media|\PHPUnit_Framework_MockObject_MockObject */
    private $swatchMediaHelper;

    /** @var \Magento\Catalog\Helper\Product|\PHPUnit_Framework_MockObject_MockObject */
    private $catalogProduct;

    /** @var \Magento\Customer\Helper\Session\CurrentCustomer|\PHPUnit_Framework_MockObject_MockObject */
    private $currentCustomer;

    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $priceCurrency;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    private $product;

    /** @var \Magento\Catalog\Model\Product\Type\AbstractType|\PHPUnit_Framework_MockObject_MockObject */
    private $typeInstance;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $scopeConfig;

    /** @var \Magento\Catalog\Helper\Image|\PHPUnit_Framework_MockObject_MockObject */
    private $imageHelper;

    /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject  */
    private $urlBuilder;

    public function setUp()
    {
        $this->arrayUtils = $this->getMock('\Magento\Framework\Stdlib\ArrayUtils', [], [], '', false);
        $this->jsonEncoder = $this->getMock('\Magento\Framework\Json\EncoderInterface', [], [], '', false);
        $this->helper = $this->getMock('\Magento\ConfigurableProduct\Helper\Data', [], [], '', false);
        $this->swatchHelper = $this->getMock('\Magento\Swatches\Helper\Data', [], [], '', false);
        $this->swatchMediaHelper = $this->getMock('\Magento\Swatches\Helper\Media', [], [], '', false);
        $this->catalogProduct = $this->getMock('\Magento\Catalog\Helper\Product', [], [], '', false);
        $this->currentCustomer = $this->getMock('\Magento\Customer\Helper\Session\CurrentCustomer', [], [], '', false);
        $this->priceCurrency = $this->getMock('\Magento\Framework\Pricing\PriceCurrencyInterface', [], [], '', false);
        $this->product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->typeInstance = $this->getMock('\Magento\Catalog\Model\Product\Type\AbstractType', [], [], '', false);
        $this->scopeConfig = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);
        $this->imageHelper = $this->getMock('\Magento\Catalog\Helper\Image', [], [], '', false);
        $this->urlBuilder = $this->getMock('\Magento\Framework\UrlInterface');

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->configurable = $objectManager->getObject(
            '\Magento\Swatches\Block\Product\Renderer\Configurable',
            [
                'scopeConfig' => $this->scopeConfig,
                'imageHelper' => $this->imageHelper,
                'urlBuilder' => $this->urlBuilder,
                'arrayUtils' => $this->arrayUtils,
                'jsonEncoder' => $this->jsonEncoder,
                'helper' => $this->helper,
                'swatchHelper' => $this->swatchHelper,
                'swatchMediaHelper' => $this->swatchMediaHelper,
                'catalogProduct' => $this->catalogProduct,
                'currentCustomer' => $this->currentCustomer,
                'priceCurrency' => $this->priceCurrency,
            ]
        );
    }

    public function testGetAndSetProduct()
    {
        $this->configurable->setProduct($this->product);

        $this->assertEquals(
            $this->product,
            $this->configurable->getProduct()
        );
    }

    public function testGetProductParent()
    {
        $this->typeInstance->expects($this->once())->method('getStoreFilter')
            ->with($this->product)
            ->willReturn(true);

        $this->product->expects($this->once())->method('getTypeInstance')
            ->willReturn($this->typeInstance);

        $this->configurable->setData('product', $this->product);

        $this->assertEquals(
            $this->product,
            $this->configurable->getProduct()
        );
    }

    public function testGetNumberSwatchesPerProduct()
    {
        $expectedValue = 123;

        $this->scopeConfig->expects($this->once())->method('getValue')
            ->with('catalog/frontend/swatches_per_product')
            ->willReturn($expectedValue);

        $this->assertEquals(
            $expectedValue,
            $this->configurable->getNumberSwatchesPerProduct()
        );
    }

    public function testSetIsProductListingContext()
    {
        $this->assertEquals(
            $this->configurable,
            $this->configurable->setIsProductListingContext(1)
        );
    }

    private function prepareGetJsonSwatchConfig()
    {
        $product1 = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $product1->expects($this->atLeastOnce())->method('isSaleable')->willReturn(true);
        $product1->expects($this->atLeastOnce())->method('getData')->with('code')->willReturn(1);

        $product2 = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $product2->expects($this->atLeastOnce())->method('isSaleable')->willReturn(true);
        $product2->expects($this->atLeastOnce())->method('getData')->with('code')->willReturn(3);

        $simpleProducts = [$product1, $product2];
        $configurableType = $this->getMock(
            '\Magento\ConfigurableProduct\Model\Product\Type\Configurable',
            [],
            [],
            '',
            false
        );
        $configurableType->expects($this->atLeastOnce())->method('getUsedProducts')->with($this->product, null)
            ->willReturn($simpleProducts);
        $this->product->expects($this->any())->method('getTypeInstance')->willReturn($configurableType);

        $productAttribute1 = $this->getMock('\Magento\Eav\Model\Entity\Attribute\AbstractAttribute', [], [], '', false);
        $productAttribute1->expects($this->any())->method('getId')->willReturn(1);
        $productAttribute1->expects($this->any())->method('getAttributeCode')->willReturn('code');

        $attribute1 = $this->getMock(
            '\Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute',
            ['getProductAttribute'],
            [],
            '',
            false
        );
        $attribute1->expects($this->any())->method('getProductAttribute')->willReturn($productAttribute1);

        $this->helper->expects($this->any())->method('getAllowAttributes')->with($this->product)
            ->willReturn([$attribute1]);
    }

    public function testGetJsonSwatchConfigNotVisualImageType()
    {
        $this->prepareGetJsonSwatchConfig();
        $this->configurable->setProduct($this->product);

        $this->swatchHelper->expects($this->once())->method('getSwatchAttributesAsArray')
            ->with($this->product)
            ->willReturn([
                1 => [
                    'options' => [1 => 'testA', 3 => 'testB'],
                    'use_product_image_for_swatch' => true,
                    'attribute_code' => 'code',
                ],
            ]);

        $this->swatchHelper->expects($this->once())->method('getSwatchesByOptionsId')
            ->with([1, 3])
            ->willReturn([
                3 => ['type' => null, 'value' => 'hello']
            ]);

        $this->swatchHelper->expects($this->once())->method('loadFirstVariationSwatchImage')
            ->with($this->product, ['code' => 3])
            ->willReturn($this->product);

        $this->product->expects($this->exactly(4))->method('getData')
            ->with('swatch_image')
            ->willReturn('/path');

        $this->imageHelper->expects($this->exactly(2))->method('init')
            ->willReturnMap([
                [$this->product, 'swatch_image', ['type' => 'swatch_image'], $this->imageHelper],
                [$this->product, 'swatch_thumb', ['type' => 'swatch_image'], $this->imageHelper],
            ]);

        $this->jsonEncoder->expects($this->once())->method('encode');

        $this->configurable->getJsonSwatchConfig();
    }

    public function testGetJsonSwatchConfigVisualImageType()
    {
        $this->prepareGetJsonSwatchConfig();
        $this->configurable->setProduct($this->product);

        $this->swatchHelper->expects($this->once())->method('getSwatchAttributesAsArray')
            ->with($this->product)
            ->willReturn([
                1 => [
                    'options' => [1 => 'testA', 3 => 'testB'],
                    'use_product_image_for_swatch' => true,
                    'attribute_code' => 'code',
                ],
            ]);

        $this->swatchHelper->expects($this->once())->method('getSwatchesByOptionsId')
            ->with([1, 3])
            ->willReturn([
                3 => ['type' => Swatch::SWATCH_TYPE_VISUAL_IMAGE, 'value' => 'hello']
            ]);

        $this->swatchHelper->expects($this->once())->method('loadFirstVariationSwatchImage')
            ->with($this->product, ['code' => 3])
            ->willReturn($this->product);

        $this->swatchMediaHelper->expects($this->exactly(2))->method('getSwatchAttributeImage')
            ->withConsecutive(
                ['swatch_image', 'hello'],
                ['swatch_thumb', 'hello']
            )
            ->willReturn('/path');

        $this->product->expects($this->exactly(6))->method('getData')
            ->withConsecutive(['swatch_image'], ['image'], ['image'], ['swatch_image'], ['image'], ['image'])
            ->will($this->onConsecutiveCalls(null, '/path', '/path', null, '/path', '/path'));

        $this->imageHelper->expects($this->exactly(2))->method('init')
            ->willReturnMap([
                [$this->product, 'swatch_image_base', ['type' => 'image'], $this->imageHelper],
                [$this->product, 'swatch_thumb_base', ['type' => 'image'], $this->imageHelper],
            ]);

        $this->jsonEncoder->expects($this->once())->method('encode');

        $this->configurable->getJsonSwatchConfig();
    }

    public function testGetJsonSwatchConfigWithoutVisualImageType()
    {
        $this->prepareGetJsonSwatchConfig();

        $this->configurable->setProduct($this->product);

        $this->swatchHelper->expects($this->once())->method('getSwatchAttributesAsArray')
            ->with($this->product)
            ->willReturn([
                1 => [
                    'options' => [1 => 'testA', 3 => 'testB'],
                    'use_product_image_for_swatch' => true,
                    'attribute_code' => 'code',
                ],
            ]);

        $this->swatchHelper->expects($this->once())->method('getSwatchesByOptionsId')
            ->with([1, 3])
            ->willReturn([
                3 => ['type' => Swatch::SWATCH_TYPE_VISUAL_IMAGE, 'value' => 'hello']
            ]);

        $this->swatchHelper->expects($this->once())->method('loadFirstVariationSwatchImage')
            ->with($this->product, ['code' => 3])
            ->willReturn($this->product);

        $this->swatchMediaHelper->expects($this->exactly(2))->method('getSwatchAttributeImage')
            ->withConsecutive(
                ['swatch_image', 'hello'],
                ['swatch_thumb', 'hello']
            )
            ->willReturn('/path');

        $this->product->expects($this->exactly(4))->method('getData')
            ->withConsecutive(['swatch_image'], ['image'], ['swatch_image'], ['image'])
            ->will($this->onConsecutiveCalls(null, null, null, null));

        $this->imageHelper->expects($this->never())->method('init');
        $this->imageHelper->expects($this->never())->method('resize');
        $this->jsonEncoder->expects($this->once())->method('encode');

        $this->configurable->getJsonSwatchConfig();
    }

    public function testGetMediaCallback()
    {
        $this->urlBuilder->expects($this->once())->method('getBaseUrl')->willReturn('http://magento.com/');
        $this->assertContains(Configurable::MEDIA_CALLBACK_ACTION, $this->configurable->getMediaCallback());
    }
}
