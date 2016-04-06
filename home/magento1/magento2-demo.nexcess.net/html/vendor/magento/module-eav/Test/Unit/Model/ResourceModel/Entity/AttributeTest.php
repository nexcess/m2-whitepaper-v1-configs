<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Eav\Test\Unit\Model\ResourceModel\Entity;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock(
            '\Magento\Framework\Model\Context',
            ['getCacheManager', 'getEventDispatcher', 'getLogger', 'getAppState', 'getActionValidator'],
            [],
            '',
            false
        );
        $eventManagerMock = $this->getMock('\Magento\Framework\Event\ManagerInterface');
        $eventManagerMock->expects($this->any())->method('dispatch');
        $this->contextMock->expects($this->any())->method('getEventDispatcher')->willReturn($eventManagerMock);
    }

    /**
     * @covers \Magento\Eav\Model\ResourceModel\Entity\Attribute::_saveOption
     */
    public function testSaveOptionSystemAttribute()
    {
        /** @var $connectionMock \PHPUnit_Framework_MockObject_MockObject */
        /** @var $resourceModelMock \Magento\Eav\Model\ResourceModel\Entity\Attribute */
        list($connectionMock, $resourceModelMock) = $this->_prepareResourceModel();

        $attributeData = [
            'attribute_id' => '123',
            'entity_type_id' => 4,
            'attribute_code' => 'status',
            'backend_model' => null,
            'backend_type' => 'int',
            'frontend_input' => 'select',
            'frontend_label' => 'Status',
            'frontend_class' => null,
            'source_model' => 'Magento\Catalog\Model\Product\Attribute\Source\Status',
            'is_required' => 1,
            'is_user_defined' => 0,
            'is_unique' => 0,
        ];

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var $model \Magento\Framework\Model\AbstractModel */
        $arguments = $objectManagerHelper->getConstructArguments('Magento\Framework\Model\AbstractModel');
        $arguments['data'] = $attributeData;
        $arguments['context'] = $this->contextMock;

        $model = $this->getMock('Magento\Framework\Model\AbstractModel', null, $arguments);
        $model->setDefault(['2']);
        $model->setOption(['delete' => [1 => '', 2 => '']]);

        $connectionMock->expects(
            $this->once()
        )->method(
            'insert'
        )->will(
            $this->returnValueMap([['eav_attribute', $attributeData, 1]])
        );

        $connectionMock->expects(
            $this->once()
        )->method(
            'fetchRow'
        )->will(
            $this->returnValueMap(
                [
                    [
                        'SELECT `eav_attribute`.* FROM `eav_attribute` ' .
                        'WHERE (attribute_code="status") AND (entity_type_id="4")',
                        $attributeData,
                    ],
                ]
            )
        );
        $connectionMock->expects(
            $this->once()
        )->method(
            'update'
        )->with(
            'eav_attribute',
            ['default_value' => 2],
            ['attribute_id = ?' => null]
        );
        $connectionMock->expects($this->never())->method('delete');

        $resourceModelMock->save($model);
    }

    /**
     * @covers \Magento\Eav\Model\ResourceModel\Entity\Attribute::_saveOption
     */
    public function testSaveOptionNewUserDefinedAttribute()
    {
        /** @var $connectionMock \PHPUnit_Framework_MockObject_MockObject */
        /** @var $resourceModelMock \Magento\Eav\Model\ResourceModel\Entity\Attribute */
        list($connectionMock, $resourceModelMock) = $this->_prepareResourceModel();

        $attributeData = [
            'entity_type_id' => 4,
            'attribute_code' => 'a_dropdown',
            'backend_model' => null,
            'backend_type' => 'int',
            'frontend_input' => 'select',
            'frontend_label' => 'A Dropdown',
            'frontend_class' => null,
            'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
            'is_required' => 0,
            'is_user_defined' => 1,
            'is_unique' => 0,
        ];

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var $model \Magento\Framework\Model\AbstractModel */
        $arguments = $objectManagerHelper->getConstructArguments('Magento\Framework\Model\AbstractModel');
        $arguments['data'] = $attributeData;
        $arguments['context'] = $this->contextMock;
        $model = $this->getMock('Magento\Framework\Model\AbstractModel', null, $arguments);
        $model->setOption(['value' => ['option_1' => ['Backend Label', 'Frontend Label']]]);

        $connectionMock->expects(
            $this->any()
        )->method(
            'lastInsertId'
        )->will(
            $this->returnValueMap([['eav_attribute', 123], ['eav_attribute_option_value', 321]])
        );
        $connectionMock->expects(
            $this->once()
        )->method(
            'update'
        )->will(
            $this->returnValueMap(
                [['eav_attribute', ['default_value' => ''], ['attribute_id = ?' => 123], 1]]
            )
        );
        $connectionMock->expects(
            $this->once()
        )->method(
            'fetchRow'
        )->will(
            $this->returnValueMap(
                [
                    [
                        'SELECT `eav_attribute`.* FROM `eav_attribute` ' .
                        'WHERE (attribute_code="a_dropdown") AND (entity_type_id="4")',
                        false,
                    ],
                ]
            )
        );
        $connectionMock->expects(
            $this->once()
        )->method(
            'delete'
        )->will(
            $this->returnValueMap([['eav_attribute_option_value', ['option_id = ?' => ''], 0]])
        );
        $connectionMock->expects(
            $this->exactly(4)
        )->method(
            'insert'
        )->will(
            $this->returnValueMap(
                [
                    ['eav_attribute', $attributeData, 1],
                    ['eav_attribute_option', ['attribute_id' => 123, 'sort_order' => 0], 1],
                    [
                        'eav_attribute_option_value',
                        ['option_id' => 123, 'store_id' => 0, 'value' => 'Backend Label'],
                        1
                    ],
                    [
                        'eav_attribute_option_value',
                        ['option_id' => 123, 'store_id' => 1, 'value' => 'Frontend Label'],
                        1
                    ],
                ]
            )
        );

        $resourceModelMock->save($model);
    }

    /**
     * @covers \Magento\Eav\Model\ResourceModel\Entity\Attribute::_saveOption
     */
    public function testSaveOptionNoValue()
    {
        /** @var $connectionMock \PHPUnit_Framework_MockObject_MockObject */
        /** @var $resourceModelMock \Magento\Eav\Model\ResourceModel\Entity\Attribute */
        list($connectionMock, $resourceModelMock) = $this->_prepareResourceModel();

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var $model \Magento\Framework\Model\AbstractModel */
        $arguments = $objectManagerHelper->getConstructArguments('Magento\Framework\Model\AbstractModel');
        $arguments['context'] = $this->contextMock;
        $model = $this->getMock('Magento\Framework\Model\AbstractModel', null, $arguments);
        $model->setOption('not-an-array');

        $connectionMock->expects($this->once())->method('insert')->with('eav_attribute');
        $connectionMock->expects($this->never())->method('delete');
        $connectionMock->expects($this->never())->method('update');

        $resourceModelMock->save($model);
    }

    /**
     * Retrieve resource model mock instance and its adapter
     *
     * @return array
     */
    protected function _prepareResourceModel()
    {
        $connectionMock = $this->getMock(
            'Magento\Framework\DB\Adapter\Pdo\Mysql',
            [
                '_connect',
                'delete',
                'describeTable',
                'fetchRow',
                'insert',
                'lastInsertId',
                'quote',
                'update',
                'beginTransaction',
                'commit',
                'rollback',
            ],
            [],
            '',
            false
        );
        $connectionMock->expects(
            $this->any()
        )->method(
            'describeTable'
        )->with(
            'eav_attribute'
        )->will(
            $this->returnValue($this->_describeEavAttribute())
        );
        $connectionMock->expects(
            $this->any()
        )->method(
            'quote'
        )->will(
            $this->returnValueMap(
                [
                    [123, 123],
                    ['4', '"4"'],
                    ['a_dropdown', '"a_dropdown"'],
                    ['status', '"status"'],
                ]
            )
        );

        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', ['getStores'], [], '', false);
        $storeManager->expects(
            $this->any()
        )->method(
            'getStores'
        )->with(
            true
        )->will(
            $this->returnValue([
                new \Magento\Framework\DataObject(['id' => 0]),
                new \Magento\Framework\DataObject(['id' => 1]), ]
            )
        );

        /** @var $resource \Magento\Framework\App\ResourceConnection */
        $resource = $this->getMock(
            'Magento\Framework\App\ResourceConnection',
            [],
            [],
            '',
            false,
            false
        );
        $resource->expects($this->any())->method('getTableName')->will($this->returnArgument(0));
        $resource->expects($this->any())->method('getConnection')->with()->will($this->returnValue($connectionMock));
        $eavEntityType = $this->getMock('Magento\Eav\Model\ResourceModel\Entity\Type', [], [], '', false, false);

        $relationProcessorMock = $this->getMock(
            '\Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor',
            [],
            [],
            '',
            false
        );

        $contextMock = $this->getMock('\Magento\Framework\Model\ResourceModel\Db\Context', [], [], '', false);
        $contextMock->expects($this->once())->method('getResources')->willReturn($resource);
        $contextMock->expects($this->once())->method('getObjectRelationProcessor')->willReturn($relationProcessorMock);

        $arguments = [
            'context' => $contextMock,
            'storeManager' => $storeManager,
            'eavEntityType' => $eavEntityType,
        ];
        $resourceModelMock = $this->getMock(
            'Magento\Eav\Model\ResourceModel\Entity\Attribute',
            ['getAdditionalAttributeTable'],
            $arguments
        );

        return [$connectionMock, $resourceModelMock];
    }

    /**
     * Retrieve eav_attribute table structure
     *
     * @return array
     */
    protected function _describeEavAttribute()
    {
        return require __DIR__ . '/../../../_files/describe_table_eav_attribute.php';
    }
}
