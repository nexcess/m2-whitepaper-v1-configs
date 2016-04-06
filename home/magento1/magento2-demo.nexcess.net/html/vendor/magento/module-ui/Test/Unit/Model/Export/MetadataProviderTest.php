<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Test\Unit\Model\Export;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class MetadataProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetadataProvider
     */
    protected $model;

    /**
     * @var Filter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    public function setUp()
    {
        $this->filter = $this->getMockBuilder('Magento\Ui\Component\MassAction\Filter')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\Ui\Model\Export\MetadataProvider',
            [
                'filter' => $this->filter,
            ]
        );
    }

    public function testGetHeaders()
    {
        $componentName = 'component_name';
        $columnName = 'column_name';
        $columnLabel = 'column_label';

        $component = $this->prepareColumns($componentName, $columnName, $columnLabel);

        $result = $this->model->getHeaders($component);
        $this->assertTrue(is_array($result));
        $this->assertCount(1, $result);
        $this->assertEquals($columnLabel, $result[0]);
    }

    public function testGetFields()
    {
        $componentName = 'component_name';
        $columnName = 'column_name';
        $columnLabel = 'column_label';

        $component = $this->prepareColumns($componentName, $columnName, $columnLabel);

        $result = $this->model->getFields($component);
        $this->assertTrue(is_array($result));
        $this->assertCount(1, $result);
        $this->assertEquals($columnName, $result[0]);
    }

    /**
     * @param $componentName
     * @param $columnName
     * @param $columnLabel
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareColumns($componentName, $columnName, $columnLabel)
    {
        $component = $this->getMockBuilder('Magento\Framework\View\Element\UiComponentInterface')
            ->getMockForAbstractClass();

        $columns = $this->getMockBuilder('Magento\Ui\Component\Listing\Columns')
            ->disableOriginalConstructor()
            ->getMock();

        $column = $this->getMockBuilder('Magento\Ui\Component\Listing\Columns\Column')
            ->disableOriginalConstructor()
            ->getMock();

        $component->expects($this->any())
            ->method('getName')
            ->willReturn($componentName);
        $component->expects($this->once())
            ->method('getChildComponents')
            ->willReturn([$columns]);

        $columns->expects($this->once())
            ->method('getChildComponents')
            ->willReturn([$column]);

        $column->expects($this->any())
            ->method('getName')
            ->willReturn($columnName);
        $column->expects($this->any())
            ->method('getData')
            ->with('config/label')
            ->willReturn($columnLabel);
        return $component;
    }

    /**
     * @param string $key
     * @param array $fields
     * @param array $options
     * @param array $expected
     * @dataProvider getRowDataProvider
     */
    public function testGetRowData($key, $fields, $options, $expected)
    {
        $document = $this->getMockBuilder('Magento\Framework\Api\Search\DocumentInterface')
            ->getMockForAbstractClass();

        $attribute = $this->getMockBuilder('Magento\Framework\Api\AttributeInterface')
            ->getMockForAbstractClass();

        $document->expects($this->once())
            ->method('getCustomAttribute')
            ->with($fields[0])
            ->willReturn($attribute);

        $attribute->expects($this->once())
            ->method('getValue')
            ->willReturn($key);

        $result = $this->model->getRowData($document, $fields, $options);
        $this->assertTrue(is_array($result));
        $this->assertCount(1, $result);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getRowDataProvider()
    {
        return [
            [
                'key' => 'key_1',
                'fields' => ['column'],
                'options' => [
                    'column' => [
                        'key_1' => 'value_1',
                    ],
                ],
                'expected' => [
                    'value_1',
                ],
            ],
            [
                'key' => 'key_2',
                'fields' => ['column'],
                'options' => [
                    'column' => [
                        'key_1' => 'value_1',
                    ],
                ],
                'expected' => [
                    '',
                ],
            ],
            [
                'key' => 'key_1',
                'fields' => ['column'],
                'options' => [],
                'expected' => [
                    'key_1',
                ],
            ],
        ];
    }

    /**
     * @param string $filter
     * @param array $options
     * @param array $expected
     * @dataProvider getOptionsDataProvider
     */
    public function testGetOptions($filter, $options, $expected)
    {
        $component = $this->getMockBuilder('Magento\Framework\View\Element\UiComponentInterface')
            ->getMockForAbstractClass();

        $childComponent = $this->getMockBuilder('Magento\Framework\View\Element\UiComponentInterface')
            ->getMockForAbstractClass();

        $filters = $this->getMockBuilder('Magento\Ui\Component\Filters')
            ->disableOriginalConstructor()
            ->getMock();

        $select = $this->getMockBuilder('Magento\Ui\Component\Filters\Type\Select')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filter->expects($this->once())
            ->method('getComponent')
            ->willReturn($component);

        $component->expects($this->once())
            ->method('getChildComponents')
            ->willReturn(['listing_top' => $childComponent]);

        $childComponent->expects($this->once())
            ->method('getChildComponents')
            ->willReturn([$filters]);

        $filters->expects($this->once())
            ->method('getChildComponents')
            ->willReturn([$select]);

        $select->expects($this->any())
            ->method('getName')
            ->willReturn($filter);
        $select->expects($this->any())
            ->method('getData')
            ->with('config/options')
            ->willReturn($options);

        $result = $this->model->getOptions();
        $this->assertTrue(is_array($result));
        $this->assertCount(1, $result);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getOptionsDataProvider()
    {
        return [
            [
                'filter' => 'filter_name',
                'options' => [
                    [
                        'value' => 'value_1',
                        'label' => 'label_1',
                    ]
                ],
                'expected' => [
                    'filter_name' => [
                        'value_1' => 'label_1',
                    ],
                ],
            ],
            [
                'filter' => 'filter_name',
                'options' => [
                    [
                        'value' => [
                            [
                                'value' => 'value_2',
                                'label' => 'label_2',
                            ],
                        ],
                        'label' => 'label_1',
                    ]
                ],
                'expected' => [
                    'filter_name' => [
                        'value_2' => 'label_1label_2',
                    ],
                ],
            ],
            [
                'filter' => 'filter_name',
                'options' => [
                    [
                        'value' => [
                            [
                                'value' => [
                                    [
                                        'value' => 'value_3',
                                        'label' => 'label_3',
                                    ]
                                ],
                                'label' => 'label_2',
                            ],
                        ],
                        'label' => 'label_1',
                    ]
                ],
                'expected' => [
                    'filter_name' => [
                        'value_3' => 'label_1label_2label_3',
                    ],
                ],
            ],
        ];
    }
}
