<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Test\Unit\Model\Order\Shipment\Track;

/**
 * Class ValidatorTest
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Order\Shipment\Track\Validator
     */
    protected $validator;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\Track|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackModelMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->trackModelMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment\Track',
            ['hasData', 'getData', '__wakeup'],
            [],
            '',
            false
        );
        $this->validator = new \Magento\Sales\Model\Order\Shipment\Track\Validator();
    }

    /**
     * Run test validate
     *
     * @param $trackDataMap
     * @param $trackData
     * @param $expectedWarnings
     * @dataProvider providerTrackData
     */
    public function testValidate($trackDataMap, $trackData, $expectedWarnings)
    {
        $this->trackModelMock->expects($this->any())
            ->method('hasData')
            ->will($this->returnValueMap($trackDataMap));
        $this->trackModelMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($trackData));
        $actualWarnings = $this->validator->validate($this->trackModelMock);
        $this->assertEquals($expectedWarnings, $actualWarnings);
    }

    /**
     * Provides track data for tests
     *
     * @return array
     */
    public function providerTrackData()
    {
        return [
            [
                [
                    ['parent_id', true],
                    ['order_id', true],
                    ['track_number', true],
                    ['carrier_code', true],
                ],
                [
                    'parent_id' => 25,
                    'order_id' => 12,
                    'track_number' => 125,
                    'carrier_code' => 'custom'
                ],
                [],
            ],
            [
                [
                    ['parent_id', true],
                    ['order_id', false],
                    ['track_number', true],
                    ['carrier_code', false],
                ],
                [
                    'parent_id' => 0,
                    'order_id' => null,
                    'track_number' => '',
                    'carrier_code' => null
                ],
                [
                    'parent_id' => 'Parent Track Id can not be empty',
                    'order_id' => 'Order Id is a required field',
                    'track_number' => 'Number can not be empty',
                    'carrier_code' => 'Carrier Code is a required field'
                ]
            ]
        ];
    }
}
