<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Unit\Model\Product\Option\Validator;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Option\Validator\Select
     */
    protected $validator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueMock;

    protected function setUp()
    {
        $configMock = $this->getMock('Magento\Catalog\Model\ProductOptions\ConfigInterface');
        $priceConfigMock = new \Magento\Catalog\Model\Config\Source\Product\Options\Price();
        $config = [
            [
                'label' => 'group label 1',
                'types' => [
                    [
                        'label' => 'label 1.1',
                        'name' => 'name 1.1',
                        'disabled' => false,
                    ],
                ],
            ],
            [
                'label' => 'group label 2',
                'types' => [
                    [
                        'label' => 'label 2.2',
                        'name' => 'name 2.2',
                        'disabled' => true,
                    ],
                ]
            ],
        ];
        $configMock->expects($this->once())->method('getAll')->will($this->returnValue($config));
        $methods = ['getTitle', 'getType', 'getPriceType', 'getPrice', '__wakeup', 'getData'];
        $this->valueMock = $this->getMock('Magento\Catalog\Model\Product\Option', $methods, [], '', false);
        $this->validator = new \Magento\Catalog\Model\Product\Option\Validator\Select(
            $configMock,
            $priceConfigMock
        );
    }

    /**
     * @param array $value
     * @dataProvider isValidSuccessDataProvider
     */
    public function testIsValidSuccess($value)
    {
        $value = [
            'price_type' => 'fixed',
            'price' => '10',
            'title' => 'Some Title',
        ];
        $this->valueMock->expects($this->once())->method('getTitle')->will($this->returnValue('option_title'));
        $this->valueMock->expects($this->exactly(2))->method('getType')->will($this->returnValue('name 1.1'));
        $this->valueMock->expects($this->never())->method('getPriceType');
        $this->valueMock->expects($this->never())->method('getPrice');
        $this->valueMock->expects($this->any())->method('getData')->with('values')->will($this->returnValue([$value]));
        $this->assertTrue($this->validator->isValid($this->valueMock));
        $this->assertEmpty($this->validator->getMessages());
    }

    public function isValidSuccessDataProvider()
    {
        $value = [
            'price_type' => 'fixed',
            'price' => '10',
            'title' => 'Some Title',
        ];

        $valueWithoutAllData = [
            'some_data' => 'data',
        ];

        return [
            'all_data' => [$value],
            'not_all_data' => [$valueWithoutAllData]
        ];
    }

    public function testIsValidateWithInvalidOptionValues()
    {
        $this->valueMock->expects($this->once())->method('getTitle')->will($this->returnValue('option_title'));
        $this->valueMock->expects($this->exactly(2))->method('getType')->will($this->returnValue('name 1.1'));
        $this->valueMock->expects($this->never())->method('getPriceType');
        $this->valueMock->expects($this->never())->method('getPrice');
        $this->valueMock
            ->expects($this->once())
            ->method('getData')
            ->with('values')
            ->will($this->returnValue('invalid_data'));
        $messages = [
            'option values' => 'Invalid option value',
        ];
        $this->assertFalse($this->validator->isValid($this->valueMock));
        $this->assertEquals($messages, $this->validator->getMessages());
    }

    public function testIsValidateWithEmptyValues()
    {
        $this->valueMock->expects($this->once())->method('getTitle')->will($this->returnValue('option_title'));
        $this->valueMock->expects($this->exactly(2))->method('getType')->will($this->returnValue('name 1.1'));
        $this->valueMock->expects($this->never())->method('getPriceType');
        $this->valueMock->expects($this->never())->method('getPrice');
        $this->valueMock->expects($this->any())->method('getData')->with('values')->will($this->returnValue([]));
        $messages = [
            'option values' => 'Invalid option value',
        ];
        $this->assertFalse($this->validator->isValid($this->valueMock));
        $this->assertEquals($messages, $this->validator->getMessages());
    }

    /**
     * @param string $priceType
     * @param int $price
     * @param string|null $title
     * @dataProvider isValidateWithInvalidDataDataProvider
     */
    public function testIsValidateWithInvalidData($priceType, $price, $title)
    {
        $value = [
            'price_type' => $priceType,
            'price' => $price,
            'title' => $title,
        ];
        $this->valueMock->expects($this->once())->method('getTitle')->will($this->returnValue('option_title'));
        $this->valueMock->expects($this->exactly(2))->method('getType')->will($this->returnValue('name 1.1'));
        $this->valueMock->expects($this->never())->method('getPriceType');
        $this->valueMock->expects($this->never())->method('getPrice');
        $this->valueMock->expects($this->any())->method('getData')->with('values')->will($this->returnValue([$value]));
        $messages = [
            'option values' => 'Invalid option value',
        ];
        $this->assertFalse($this->validator->isValid($this->valueMock));
        $this->assertEquals($messages, $this->validator->getMessages());
    }

    public function isValidateWithInvalidDataDataProvider()
    {
        return [
            'invalid_price' => ['fixed', -10, 'Title'],
            'invalid_price_type' => ['some_value', '10', 'Title'],
            'empty_title' => ['fixed', 10, null]
        ];
    }
}
