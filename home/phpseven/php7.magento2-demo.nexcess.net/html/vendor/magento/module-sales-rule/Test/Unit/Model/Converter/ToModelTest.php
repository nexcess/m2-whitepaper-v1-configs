<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRule\Test\Unit\Model\Converter;

class ToModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\SalesRule\Model\Converter\ToModel
     */
    protected $model;

    protected function setUp()
    {
        $this->ruleFactory = $this->getMockBuilder('Magento\SalesRule\Model\RuleFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->dataObjectProcessor = $this->getMockBuilder('\Magento\Framework\Reflection\DataObjectProcessor')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $helper->getObject(
            'Magento\SalesRule\Model\Converter\ToModel',
            [
                'ruleFactory' =>  $this->ruleFactory,
                'dataObjectProcessor' => $this->dataObjectProcessor,
            ]
        );
    }

    public function testDataModelToArray()
    {
        $array = [
            'type' => 'conditionType',
            'value' => 'value',
            'attribute' => 'getAttributeName',
            'operator' => 'getOperator',
            'aggregator' => 'getAggregatorType',
            'conditions' => [
                [
                    'type' => null,
                    'value' => null,
                    'attribute' => null,
                    'operator' => null,
                ],
                [
                    'type' => null,
                    'value' => null,
                    'attribute' => null,
                    'operator' => null,
                ],
            ],
        ];

        /**
         * @var \Magento\SalesRule\Model\Data\Condition $dataCondition
         */
        $dataCondition = $this->getMockBuilder('\Magento\SalesRule\Model\Data\Condition')
            ->disableOriginalConstructor()
            ->setMethods(['create', 'load', 'getConditionType', 'getValue', 'getAttributeName', 'getOperator',
                'getAggregatorType', 'getConditions'])
            ->getMock();

        $dataCondition
            ->expects($this->atLeastOnce())
            ->method('getConditionType')
            ->willReturn('conditionType');

        $dataCondition
            ->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn('value');

        $dataCondition
            ->expects($this->atLeastOnce())
            ->method('getAttributeName')
            ->willReturn('getAttributeName');

        $dataCondition
            ->expects($this->atLeastOnce())
            ->method('getOperator')
            ->willReturn('getOperator');

        $dataCondition
            ->expects($this->atLeastOnce())
            ->method('getAggregatorType')
            ->willReturn('getAggregatorType');


        $dataCondition1 = $this->getMockBuilder('\Magento\SalesRule\Model\Data\Condition')
            ->disableOriginalConstructor()
            ->setMethods(['create', 'load', 'getConditionType', 'getValue', 'getAttributeName', 'getOperator',
                'getAggregatorType', 'getConditions'])
            ->getMock();

        $dataCondition2 = $this->getMockBuilder('\Magento\SalesRule\Model\Data\Condition')
            ->disableOriginalConstructor()
            ->setMethods(['create', 'load', 'getConditionType', 'getValue', 'getAttributeName', 'getOperator',
                'getAggregatorType', 'getConditions'])
            ->getMock();

        $dataCondition
            ->expects($this->atLeastOnce())
            ->method('getConditions')
            ->willReturn([$dataCondition1, $dataCondition2]);

        $result = $this->model->dataModelToArray($dataCondition);

        $this->assertEquals($array, $result);
    }

    public function testToModel()
    {
        /**
         * @var \Magento\SalesRule\Model\Data\Rule $dataModel
         */
        $dataModel = $this->getMockBuilder('\Magento\SalesRule\Model\Data\Rule')
            ->disableOriginalConstructor()
            ->setMethods(['create', 'load', 'getData', 'getRuleId', 'getCondition', 'getActionCondition',
                'getStoreLabels'])
            ->getMock();
        $dataModel
            ->expects($this->atLeastOnce())
            ->method('getRuleId')
            ->willReturn(1);

        $dataModel
            ->expects($this->atLeastOnce())
            ->method('getCondition')
            ->willReturn(false);

        $dataModel
            ->expects($this->atLeastOnce())
            ->method('getActionCondition')
            ->willReturn(false);

        $dataModel
            ->expects($this->atLeastOnce())
            ->method('getStoreLabels')
            ->willReturn([]);

        $ruleModel = $this->getMockBuilder('\Magento\SalesRule\Model\Rule')
            ->disableOriginalConstructor()
            ->setMethods(['create', 'load', 'getId', 'getData'])
            ->getMock();

        $ruleModel
            ->expects($this->atLeastOnce())
            ->method('load')
            ->willReturn($ruleModel);
        $ruleModel
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(1);

        $ruleModel
            ->expects($this->atLeastOnce())
            ->method('getData')
            ->willReturn(['data_1'=>1]);

         $this->dataObjectProcessor
             ->expects($this->any())
             ->method('buildOutputDataArray')
             ->willReturn(['data_2'=>2]);

        $this->ruleFactory
            ->expects($this->any())
            ->method('create')
            ->willReturn($ruleModel);

        $result = $this->model->toModel($dataModel);
        $this->assertEquals($ruleModel, $result);
    }
}
