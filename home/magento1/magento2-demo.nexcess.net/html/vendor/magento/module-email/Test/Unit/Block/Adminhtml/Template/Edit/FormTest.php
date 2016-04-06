<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Email\Test\Unit\Block\Adminhtml\Template\Edit;

/**
 * @covers \Magento\Email\Block\Adminhtml\Template\Edit\Form
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Email\Block\Adminhtml\Template\Edit\Form */
    protected $form;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registryMock;

    /** @var \Magento\Email\Model\Source\Variables|\PHPUnit_Framework_MockObject_MockObject */
    protected $variablesMock;

    /** @var \Magento\Variable\Model\VariableFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $variableFactoryMock;

    /** @var \Magento\Variable\Model\Variable|\PHPUnit_Framework_MockObject_MockObject */
    protected $variableMock;

    /** @var \Magento\Email\Model\Template|\PHPUnit_Framework_MockObject_MockObject */
    protected $templateMock;

    public function setUp()
    {
        $this->registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->setMethods(['registry'])
            ->getMock();
        $this->variablesMock = $this->getMockBuilder('Magento\Email\Model\Source\Variables')
            ->disableOriginalConstructor()
            ->setMethods(['toOptionArray'])
            ->getMock();
        $this->variableFactoryMock = $this->getMockBuilder('Magento\Variable\Model\VariableFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->variableMock = $this->getMockBuilder('Magento\Variable\Model\Variable')
            ->disableOriginalConstructor()
            ->setMethods(['getVariablesOptionArray'])
            ->getMock();
        $this->templateMock = $this->getMockBuilder('Magento\Email\Model\Template')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getVariablesOptionArray'])
            ->getMock();
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->form = $objectManager->getObject(
            'Magento\Email\Block\Adminhtml\Template\Edit\Form',
            [
                'registry' => $this->registryMock,
                'variableFactory' => $this->variableFactoryMock,
                'variables' => $this->variablesMock
            ]
        );
    }

    /**
     * @covers \Magento\Email\Block\Adminhtml\Template\Edit\Form::getVariables
     */
    public function testGetVariables()
    {
        $this->variablesMock->expects($this->once())
            ->method('toOptionArray')
            ->willReturn(['var1', 'var2', 'var3']);
        $this->variableFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->variableMock);
        $this->variableMock->expects($this->once())
            ->method('getVariablesOptionArray')
            ->willReturn(['custom var 1', 'custom var 2']);
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->willReturn($this->templateMock);
        $this->templateMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $this->templateMock->expects($this->once())
            ->method('getVariablesOptionArray')
            ->willReturn(['template var 1', 'template var 2']);
        $this->assertEquals(
            [['var1', 'var2', 'var3'], ['custom var 1', 'custom var 2'], ['template var 1', 'template var 2']],
            $this->form->getVariables()
        );
    }

    /**
     * @covers \Magento\Email\Block\Adminhtml\Template\Edit\Form::getEmailTemplate
     */
    public function testGetEmailTemplate()
    {
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('current_email_template');
        $this->form->getEmailTemplate();
    }
}
