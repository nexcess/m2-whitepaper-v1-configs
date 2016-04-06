<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Config\Test\Unit\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Config\Model\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_structureReaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_transFactoryMock;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataFactoryMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $_configStructure;

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMock(
            'Magento\Framework\Event\ManagerInterface',
            [],
            [],
            '',
            false
        );
        $this->_structureReaderMock = $this->getMock(
            'Magento\Config\Model\Config\Structure\Reader',
            [],
            [],
            '',
            false
        );
        $this->_configStructure = $this->getMock(
            'Magento\Config\Model\Config\Structure',
            [],
            [],
            '',
            false
        );

        $this->_structureReaderMock->expects(
            $this->any()
        )->method(
            'getConfiguration'
        )->will(
            $this->returnValue($this->_configStructure)
        );

        $this->_transFactoryMock = $this->getMock(
            'Magento\Framework\DB\TransactionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->_appConfigMock = $this->getMock('Magento\Framework\App\Config\ReinitableConfigInterface');
        $this->_configLoaderMock = $this->getMock(
            'Magento\Config\Model\Config\Loader',
            ['getConfigByPath'],
            [],
            '',
            false
        );
        $this->_dataFactoryMock = $this->getMock(
            'Magento\Framework\App\Config\ValueFactory',
            [],
            [],
            '',
            false
        );

        $this->_storeManager = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');

        $this->_model = new \Magento\Config\Model\Config(
            $this->_appConfigMock,
            $this->_eventManagerMock,
            $this->_configStructure,
            $this->_transFactoryMock,
            $this->_configLoaderMock,
            $this->_dataFactoryMock,
            $this->_storeManager
        );
    }

    public function testSaveDoesNotDoAnythingIfGroupsAreNotPassed()
    {
        $this->_configLoaderMock->expects($this->never())->method('getConfigByPath');
        $this->_model->save();
    }

    public function testSaveEmptiesNonSetArguments()
    {
        $this->_structureReaderMock->expects($this->never())->method('getConfiguration');
        $this->assertNull($this->_model->getSection());
        $this->assertNull($this->_model->getWebsite());
        $this->assertNull($this->_model->getStore());
        $this->_model->save();
        $this->assertSame('', $this->_model->getSection());
        $this->assertSame('', $this->_model->getWebsite());
        $this->assertSame('', $this->_model->getStore());
    }

    public function testSaveToCheckAdminSystemConfigChangedSectionEvent()
    {
        $transactionMock = $this->getMock('Magento\Framework\DB\Transaction', [], [], '', false);

        $this->_transFactoryMock->expects($this->any())->method('create')->will($this->returnValue($transactionMock));

        $this->_configLoaderMock->expects($this->any())->method('getConfigByPath')->will($this->returnValue([]));

        $this->_eventManagerMock->expects(
            $this->at(0)
        )->method(
            'dispatch'
        )->with(
            $this->equalTo('admin_system_config_changed_section_'),
            $this->arrayHasKey('website')
        );

        $this->_eventManagerMock->expects(
            $this->at(0)
        )->method(
            'dispatch'
        )->with(
            $this->equalTo('admin_system_config_changed_section_'),
            $this->arrayHasKey('store')
        );

        $this->_model->setGroups(['1' => ['data']]);
        $this->_model->save();
    }

    public function testSaveToCheckScopeDataSet()
    {
        $transactionMock = $this->getMock('Magento\Framework\DB\Transaction', [], [], '', false);

        $this->_transFactoryMock->expects($this->any())->method('create')->will($this->returnValue($transactionMock));

        $this->_configLoaderMock->expects($this->any())->method('getConfigByPath')->will($this->returnValue([]));

        $this->_eventManagerMock->expects(
            $this->at(0)
        )->method(
            'dispatch'
        )->with(
            $this->equalTo('admin_system_config_changed_section_'),
            $this->arrayHasKey('website')
        );

        $this->_eventManagerMock->expects(
            $this->at(0)
        )->method(
            'dispatch'
        )->with(
            $this->equalTo('admin_system_config_changed_section_'),
            $this->arrayHasKey('store')
        );

        $group = $this->getMock('Magento\Config\Model\Config\Structure\Element\Group', [], [], '', false);

        $field = $this->getMock('Magento\Config\Model\Config\Structure\Element\Field', [], [], '', false);

        $this->_configStructure->expects(
            $this->at(0)
        )->method(
            'getElement'
        )->with(
            '/1'
        )->will(
            $this->returnValue($group)
        );

        $this->_configStructure->expects(
            $this->at(1)
        )->method(
            'getElement'
        )->with(
            '/1/key'
        )->will(
            $this->returnValue($field)
        );

        $website = $this->getMock('Magento\Store\Model\Website', [], [], '', false);
        $website->expects($this->any())->method('getCode')->will($this->returnValue('website_code'));
        $this->_storeManager->expects($this->any())->method('getWebsite')->will($this->returnValue($website));
        $this->_storeManager->expects($this->any())->method('getWebsites')->will($this->returnValue([$website]));
        $this->_storeManager->expects($this->any())->method('isSingleStoreMode')->will($this->returnValue(true));

        $this->_model->setWebsite('website');

        $this->_model->setGroups(['1' => ['fields' => ['key' => ['data']]]]);

        $backendModel = $this->getMock(
            'Magento\Framework\App\Config\Value',
            ['setPath', 'addData', '__sleep', '__wakeup'],
            [],
            '',
            false
        );
        $backendModel->expects(
            $this->once()
        )->method(
            'addData'
        )->with(
            [
                'field' => 'key',
                'groups' => [1 => ['fields' => ['key' => ['data']]]],
                'group_id' => null,
                'scope' => 'websites',
                'scope_id' => 0,
                'scope_code' => 'website_code',
                'field_config' => null,
                'fieldset_data' => ['key' => null],
            ]
        );
        $backendModel->expects(
            $this->once()
        )->method(
            'setPath'
        )->with(
            '/key'
        )->will(
            $this->returnValue($backendModel)
        );

        $this->_dataFactoryMock->expects($this->any())->method('create')->will($this->returnValue($backendModel));

        $this->_model->save();
    }

    public function testSetDataByPath()
    {
        $value = 'value';
        $path = '<section>/<group>/<field>';
        $this->_model->setDataByPath($path, $value);
        $expected = [
            'section' => '<section>',
            'groups' => [
                '<group>' => [
                    'fields' => [
                        '<field>' => ['value' => $value],
                    ],
                ],
            ],
        ];
        $this->assertSame($expected, $this->_model->getData());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Path must not be empty
     */
    public function testSetDataByPathEmpty()
    {
        $this->_model->setDataByPath('', 'value');
    }

    /**
     * @param string $path
     * @param string $expectedException
     *
     * @dataProvider setDataByPathWrongDepthDataProvider
     */
    public function testSetDataByPathWrongDepth($path, $expectedException)
    {
        $expectedException = 'Allowed depth of configuration is 3 (<section>/<group>/<field>). ' . $expectedException;
        $this->setExpectedException('\UnexpectedValueException', $expectedException);
        $value = 'value';
        $this->_model->setDataByPath($path, $value);
    }

    /**
     * @return array
     */
    public function setDataByPathWrongDepthDataProvider()
    {
        return [
            'depth 2' => ['section/group', "Your configuration depth is 2 for path 'section/group'"],
            'depth 1' => ['section', "Your configuration depth is 1 for path 'section'"],
            'depth 4' => ['section/group/field/sub-field', "Your configuration depth is 4 for path"
            . " 'section/group/field/sub-field'", ],
        ];
    }
}
