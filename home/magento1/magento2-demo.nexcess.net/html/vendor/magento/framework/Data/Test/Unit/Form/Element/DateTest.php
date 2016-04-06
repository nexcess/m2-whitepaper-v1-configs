<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Date
 */
namespace Magento\Framework\Data\Test\Unit\Form\Element;

use \Magento\Framework\Data\Form\Element\Date;

class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Date
     */
    protected $model;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factoryMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    protected function setUp()
    {
        $this->factoryMock = $this->getMock('Magento\Framework\Data\Form\Element\Factory', [], [], '', false);
        $this->collectionFactoryMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            [],
            [],
            '',
            false
        );
        $this->escaperMock = $this->getMock('Magento\Framework\Escaper', [], [], '', false);
        $this->localeDateMock = $this->getMock(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
            [],
            [],
            '',
            false
        );
        $this->model = new Date(
            $this->factoryMock,
            $this->collectionFactoryMock,
            $this->escaperMock,
            $this->localeDateMock
        );
    }

    public function testGetElementHtmlException()
    {
        $this->setExpectedException(
            'Exception',
            'Output format is not specified. Please, specify "format" key in constructor, or set it using setFormat().'
        );
        $formMock = $this->getFormMock('never');
        $this->model->setForm($formMock);
        $this->model->getElementHtml();
    }

    /**
     * @param $fieldName
     * @dataProvider providerGetElementHtmlDateFormat
     */
    public function testGetElementHtmlDateFormat($fieldName)
    {
        $formMock = $this->getFormMock('once');
        $this->model->setForm($formMock);

        $this->model->setData([
                $fieldName => 'yyyy-MM-dd',
                'name' => 'test_name',
                'html_id' => 'test_name',
            ]);
        $this->model->getElementHtml();
    }

    public function providerGetElementHtmlDateFormat()
    {
        return [
            ['date_format'],
            ['format'],
        ];
    }

    protected function getFormMock($exactly)
    {
        $functions = ['getFieldNameSuffix', 'getHtmlIdPrefix', 'getHtmlIdSuffix'];
        $formMock = $this->getMock('stdClass', $functions);
        foreach ($functions as $method) {
            switch ($exactly) {
                case 'once':
                    $count = $this->once();
                    break;
                case 'never':
                default:
                    $count = $this->never();
            }
            $formMock->expects($count)->method($method);
        }

        return $formMock;
    }
}
