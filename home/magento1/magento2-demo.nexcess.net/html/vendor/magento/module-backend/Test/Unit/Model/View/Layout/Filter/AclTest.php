<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Test\Unit\Model\View\Layout\Filter;

class AclTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\View\Layout\Filter\Acl
     */
    protected $model;

    /**
     * @var \Magento\Framework\AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationMock;

    protected function setUp()
    {
        $this->authorizationMock = $this->getMockBuilder('Magento\Framework\AuthorizationInterface')
            ->getMock();

        $this->model = new \Magento\Backend\Model\View\Layout\Filter\Acl($this->authorizationMock);
    }

    public function testFilterAclElements()
    {
        $scheduledStructureMock = $this->getMockBuilder('Magento\Framework\View\Layout\ScheduledStructure')
            ->disableOriginalConstructor()
            ->getMock();

        $structureMock = $this->getMockBuilder('Magento\Framework\View\Layout\Data\Structure')
            ->disableOriginalConstructor()
            ->getMock();


        $elements = [
            'element_0' => [
                0 => '',
                1 => [
                    'attributes' => [
                        'name' => 'element_0',
                    ],
                ],
            ],
            'element_1' => [
                0 => '',
                1 => [
                    'attributes' => [
                        'name' => 'element_1',
                        'acl' => 'acl_authorised',
                    ],
                ],
            ],
            'element_2' => [
                0 => '',
                1 => [
                    'attributes' => [
                        'name' => 'element_2',
                        'acl' => 'acl_non_authorised',
                    ],
                ],
            ],
            'element_3' => [
                0 => '',
                1 => [
                    'attributes' => [
                        'name' => 'element_3',
                        'acl' => 'acl_non_authorised',
                    ],
                ],
            ],
        ];

        $scheduledStructureMock->expects($this->once())
            ->method('getElements')
            ->willReturn($elements);

        $this->authorizationMock->expects($this->exactly(3))
            ->method('isAllowed')
            ->willReturnMap(
                [
                    ['acl_authorised', null, true],
                    ['acl_non_authorised', null, false],
                ]
            );

        $structureMock->expects($this->exactly(3))
            ->method('getChildren')
            ->willReturnMap(
                [
                    ['element_2', ['element_2_child' => []]],
                    ['element_2_child', []],
                    ['element_3', []],
                ]
            );

        $scheduledStructureMock->expects($this->exactly(3))
            ->method('unsetElement')
            ->willReturnMap(
                [
                    ['element_2', null],
                    ['element_2_child', null],
                    ['element_3', null],
                ]
            );

        $structureMock->expects($this->exactly(2))
            ->method('unsetElement')
            ->willReturnMap(
                [
                    ['element_2', true, true],
                    ['element_3', true, true],
                ]
            );

        $this->model->filterAclElements($scheduledStructureMock, $structureMock);
    }
}
