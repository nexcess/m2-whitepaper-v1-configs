<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Catalog\Test\Unit\Model\Product\Attribute;

use \Magento\Catalog\Model\Product\Attribute\TypesList;

class TypesListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypesList
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $inputTypeFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeTypeFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelperMock;

    protected function setUp()
    {
        $this->inputTypeFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory',
            ['create', '__wakeup'],
            [],
            '',
            false);
        $this->attributeTypeFactoryMock =
            $this->getMock(
                'Magento\Catalog\Api\Data\ProductAttributeTypeInterfaceFactory',
                [
                    'create',
                ],
                [],
                '',
                false);

        $this->dataObjectHelperMock = $this->getMockBuilder('\Magento\Framework\Api\DataObjectHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = new TypesList(
            $this->inputTypeFactoryMock,
            $this->attributeTypeFactoryMock,
            $this->dataObjectHelperMock
        );
    }

    public function testGetItems()
    {
        $inputTypeMock = $this->getMock('Magento\Catalog\Model\Product\Attribute\Source\Inputtype', [], [], '', false);
        $this->inputTypeFactoryMock->expects($this->once())->method('create')->willReturn($inputTypeMock);
        $inputTypeMock->expects($this->once())->method('toOptionArray')->willReturn(['option' => ['value']]);
        $attributeTypeMock = $this->getMock('\Magento\Catalog\Api\Data\ProductAttributeTypeInterface');
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($attributeTypeMock, ['value'], '\Magento\Catalog\Api\Data\ProductAttributeTypeInterface')
            ->willReturnSelf();
        $this->attributeTypeFactoryMock->expects($this->once())->method('create')->willReturn($attributeTypeMock);
        $this->assertEquals([$attributeTypeMock], $this->model->getItems());
    }
}
