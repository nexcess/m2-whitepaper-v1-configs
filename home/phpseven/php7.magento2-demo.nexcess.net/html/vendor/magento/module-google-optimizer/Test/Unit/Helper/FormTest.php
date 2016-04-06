<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleOptimizer\Test\Unit\Helper;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GoogleOptimizer\Helper\Form
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldsetMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_experimentCodeMock;

    protected function setUp()
    {
        $this->_formMock = $this->getMock(
            'Magento\Framework\Data\Form',
            ['setFieldNameSuffix', 'addFieldset'],
            [],
            '',
            false
        );
        $this->_fieldsetMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\Fieldset',
            [],
            [],
            '',
            false
        );
        $this->_experimentCodeMock = $this->getMock(
            'Magento\GoogleOptimizer\Model\Code',
            ['getExperimentScript', 'getCodeId', '__wakeup'],
            [],
            '',
            false
        );
        $context = $this->getMock('Magento\Framework\App\Helper\Context', [], [], '', false);
        $data = ['context' => $context];
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_helper = $objectManagerHelper->getObject('Magento\GoogleOptimizer\Helper\Form', $data);
    }

    public function testAddFieldsWithExperimentCode()
    {
        $experimentCode = 'some-code';
        $experimentCodeId = 'code-id';
        $this->_experimentCodeMock->expects(
            $this->once()
        )->method(
            'getExperimentScript'
        )->will(
            $this->returnValue($experimentCode)
        );
        $this->_experimentCodeMock->expects(
            $this->once()
        )->method(
            'getCodeId'
        )->will(
            $this->returnValue($experimentCodeId)
        );
        $this->_prepareFormMock($experimentCode, $experimentCodeId);

        $this->_helper->addGoogleoptimizerFields($this->_formMock, $this->_experimentCodeMock);
    }

    public function testAddFieldsWithoutExperimentCode()
    {
        $experimentCode = '';
        $experimentCodeId = '';
        $this->_prepareFormMock($experimentCode, $experimentCodeId);

        $this->_helper->addGoogleoptimizerFields($this->_formMock, null);
    }

    /**
     * @param string|array $experimentCode
     * @param string $experimentCodeId
     */
    protected function _prepareFormMock($experimentCode, $experimentCodeId)
    {
        $this->_formMock->expects(
            $this->once()
        )->method(
            'addFieldset'
        )->with(
            'googleoptimizer_fields',
            ['legend' => 'Google Analytics Content Experiments Code']
        )->will(
            $this->returnValue($this->_fieldsetMock)
        );

        $this->_fieldsetMock->expects(
            $this->at(0)
        )->method(
            'addField'
        )->with(
            'experiment_script',
            'textarea',
            [
                'name' => 'experiment_script',
                'label' => 'Experiment Code',
                'value' => $experimentCode,
                'class' => 'textarea googleoptimizer',
                'required' => false,
                'note' => 'Experiment code should be added to the original page only.'
            ]
        );

        $this->_fieldsetMock->expects(
            $this->at(1)
        )->method(
            'addField'
        )->with(
            'code_id',
            'hidden',
            ['name' => 'code_id', 'value' => $experimentCodeId, 'required' => false]
        );
        $this->_formMock->expects($this->once())->method('setFieldNameSuffix')->with('google_experiment');
    }
}
