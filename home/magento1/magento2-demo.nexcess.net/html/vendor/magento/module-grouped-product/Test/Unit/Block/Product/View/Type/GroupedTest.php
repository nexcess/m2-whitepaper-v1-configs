<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GroupedProduct\Test\Unit\Block\Product\View\Type;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Block\Product\View\Type\Grouped
     */
    protected $groupedView;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeInstanceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuredValueMock;

    protected function setUp()
    {
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $methodsProduct = [
            'getId',
            'setQty',
            'getTypeInstance',
            'getPreconfiguredValues',
            'getTypeId',
            '__wakeup',
        ];
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', $methodsProduct, [], '', false);
        $this->typeInstanceMock = $this->getMock(
            'Magento\GroupedProduct\Model\Product\Type\Grouped',
            [],
            [],
            '',
            false
        );
        $this->productMock->expects(
            $this->any()
        )->method(
            'getTypeInstance'
        )->will(
            $this->returnValue($this->typeInstanceMock)
        );
        $this->configuredValueMock = $this->getMock(
            'Magento\Framework\DataObject',
            ['getSuperGroup'],
            [],
            '',
            false
        );
        $layout = $this->getMock('Magento\Framework\View\LayoutInterface');
        $this->groupedView = $helper->getObject(
            'Magento\GroupedProduct\Block\Product\View\Type\Grouped',
            [
                'data' => ['product' => $this->productMock],
                'layout' => $layout
            ]
        );
    }

    public function testGetAssociatedProducts()
    {
        $this->typeInstanceMock->expects(
            $this->once()
        )->method(
            'getAssociatedProducts'
        )->with(
            $this->productMock
        )->will(
            $this->returnValue('expected')
        );

        $this->assertEquals('expected', $this->groupedView->getAssociatedProducts());
    }

    /**
     * @param string $id
     * @dataProvider setPreconfiguredValueDataProvider
     */
    public function testSetPreconfiguredValue($id)
    {
        $configValue = ['id_one' => 2];
        $associatedProduct = ['key' => $this->productMock];
        $this->configuredValueMock->expects(
            $this->once()
        )->method(
            'getSuperGroup'
        )->will(
            $this->returnValue($configValue)
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'getPreconfiguredValues'
        )->will(
            $this->returnValue($this->configuredValueMock)
        );

        $this->typeInstanceMock->expects(
            $this->once()
        )->method(
            'getAssociatedProducts'
        )->with(
            $this->productMock
        )->will(
            $this->returnValue($associatedProduct)
        );

        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue($id));
        $this->productMock->expects($this->any())->method('setQty')->with(2);
        $this->groupedView->setPreconfiguredValue();
    }

    public function setPreconfiguredValueDataProvider()
    {
        return ['item_id_exist_in_config' => ['id_one'], 'item_id_not_exist_in_config' => ['id_two']];
    }

    public function testSetPreconfiguredValueIfSuperGroupNotExist()
    {
        $this->productMock->expects(
            $this->once()
        )->method(
            'getPreconfiguredValues'
        )->will(
            $this->returnValue($this->configuredValueMock)
        );
        $this->configuredValueMock->expects($this->once())->method('getSuperGroup')->will($this->returnValue(false));
        $this->typeInstanceMock->expects($this->never())->method('getAssociatedProducts');
        $this->groupedView->setPreconfiguredValue();
    }
}
