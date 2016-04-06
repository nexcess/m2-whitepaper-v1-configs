<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Config\Test\Unit\Model\Config\Structure\Element;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class SectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Config\Model\Config\Structure\Element\Section
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $this->_authorizationMock = $this->getMock('Magento\Framework\AuthorizationInterface');

        $this->_model = $objectManager->getObject(
            'Magento\Config\Model\Config\Structure\Element\Section',
            [
                'storeManager' => $this->_storeManagerMock,
                'authorization' => $this->_authorizationMock,
            ]
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_storeManagerMock);
        unset($this->_authorizationMock);
    }

    public function testIsAllowedReturnsFalseIfNoResourceIsSpecified()
    {
        $this->assertFalse($this->_model->isAllowed());
    }

    public function testIsAllowedReturnsTrueIfResourcesIsValidAndAllowed()
    {
        $this->_authorizationMock->expects(
            $this->once()
        )->method(
            'isAllowed'
        )->with(
            'someResource'
        )->will(
            $this->returnValue(true)
        );

        $this->_model->setData(['resource' => 'someResource'], 'store');
        $this->assertTrue($this->_model->isAllowed());
    }

    public function testIsVisibleFirstChecksIfSectionIsAllowed()
    {
        $this->_storeManagerMock->expects($this->never())->method('isSingleStoreMode');
        $this->assertFalse($this->_model->isVisible());
    }

    public function testIsVisibleProceedsWithVisibilityCheckIfSectionIsAllowed()
    {
        $this->_authorizationMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->_storeManagerMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->_model->setData(['resource' => 'Magento_Backend::all'], 'scope');
        $this->_model->isVisible();
    }
}
