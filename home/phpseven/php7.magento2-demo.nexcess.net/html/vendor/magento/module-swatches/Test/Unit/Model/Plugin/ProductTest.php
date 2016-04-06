<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Swatches\Test\Unit\Model\Plugin;

/**
 * Class Product for changing image roles list
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataRoles
     */
    public function testAfterGetMediaAttributes($productType, $hasKey)
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', ['getTypeId'], [], '', false);
        $roleMock = $this->getMock('\Magento\Catalog\Model\ResourceModel\Eav\Attribute', [], [], '', false);

        $imageRolesArray = [
            'image' => $roleMock,
            'small_image' => $roleMock,
            'thumbnail' => $roleMock,
            'swatch_image' => $roleMock,
        ];

        $plugin = $objectManager->getObject('\Magento\Swatches\Model\Plugin\Product');

        $productMock->expects($this->atLeastOnce())->method('getTypeId')->willReturn($productType);

        $result = $plugin->afterGetMediaAttributes($productMock, $imageRolesArray);

        if ($hasKey) {
            $this->assertArrayHasKey('swatch_image', $result);
        } else {
            $this->assertArrayNotHasKey('swatch_image', $result);
        }
    }

    public function dataRoles()
    {
        return [
            [
                'configurable',
                false,
            ],
            [
                'simple',
                true,
            ],
            [
                'virtual',
                true,
            ],
            [
                'bundle',
                false,
            ],
            [
                'grouped',
                false,
            ],
            [
                'downloadable',
                false,
            ],
            [
                'custom_product_type',
                false,
            ],
        ];
    }
}
