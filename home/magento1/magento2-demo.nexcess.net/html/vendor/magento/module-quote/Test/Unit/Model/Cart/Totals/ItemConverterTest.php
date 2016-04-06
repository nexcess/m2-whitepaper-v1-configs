<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Quote\Test\Unit\Model\Cart\Totals;

class ItemConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $totalsFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectHelperMock;

    /**
     * @var \Magento\Quote\Model\Cart\Totals\ItemConverter
     */
    private $model;

    public function setUp()
    {
        $this->configPoolMock = $this->getMock('Magento\Catalog\Helper\Product\ConfigurationPool', [], [], '', false);
        $this->eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->dataObjectHelperMock = $this->getMock('Magento\Framework\Api\DataObjectHelper', [], [], '', false);
        $this->totalsFactoryMock = $this->getMock(
            'Magento\Quote\Api\Data\TotalsItemInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->model = new \Magento\Quote\Model\Cart\Totals\ItemConverter(
            $this->configPoolMock,
            $this->eventManagerMock,
            $this->totalsFactoryMock,
            $this->dataObjectHelperMock
        );
    }

    public function testModelToDataObject()
    {
        $productType = 'simple';

        $itemMock = $this->getMock('Magento\Quote\Model\Quote\Item', [], [], '', false);
        $itemMock->expects($this->once())->method('toArray')->will($this->returnValue(['options' => []]));
        $itemMock->expects($this->any())->method('getProductType')->will($this->returnValue($productType));

        $simpleConfigMock = $this->getMock('Magento\Catalog\Helper\Product\Configuration', [], [], '', false);
        $defaultConfigMock = $this->getMock('Magento\Catalog\Helper\Product\Configuration', [], [], '', false);

        $this->configPoolMock->expects($this->any())->method('getByProductType')
            ->will($this->returnValueMap([['simple', $simpleConfigMock], ['default', $defaultConfigMock]]));

        $options = ['1' => ['label' => 'option1'], '2' => ['label' => 'option2']];
        $simpleConfigMock->expects($this->once())->method('getOptions')->with($itemMock)
            ->will($this->returnValue($options));

        $option = ['data' => 'optionsData', 'label' => ''];
        $defaultConfigMock->expects($this->any())->method('getFormattedOptionValue')->will($this->returnValue($option));

        $this->eventManagerMock->expects($this->once())->method('dispatch')
            ->with('items_additional_data', ['item' => $itemMock]);

        $this->totalsFactoryMock->expects($this->once())->method('create');

        $expectedData = [
            'options' => '{"1":{"data":"optionsData","label":"option1"},"2":{"data":"optionsData","label":"option2"}}'
        ];
        $this->dataObjectHelperMock->expects($this->once())->method('populateWithArray')
            ->with(null, $expectedData, '\Magento\Quote\Api\Data\TotalsItemInterface');

        $this->model->modelToDataObject($itemMock);
    }
}
