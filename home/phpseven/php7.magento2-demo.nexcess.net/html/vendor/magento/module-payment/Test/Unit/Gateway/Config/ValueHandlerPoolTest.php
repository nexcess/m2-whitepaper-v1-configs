<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payment\Test\Unit\Gateway\Config;

use Magento\Payment\Gateway\Config\ValueHandlerInterface;
use Magento\Payment\Gateway\Config\ValueHandlerPool;

class ValueHandlerPoolTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorException()
    {
        $this->setExpectedException('LogicException');
        $tMapFactory = $this->getMockBuilder('Magento\Framework\ObjectManager\TMapFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $tMapFactory->expects(static::never())
            ->method('create');
        new ValueHandlerPool($tMapFactory, []);
    }

    public function testGet()
    {
        $defaultHandler = $this->getMockBuilder('Magento\Payment\Gateway\Config\ValueHandlerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $someValueHandler = $this->getMockBuilder('Magento\Payment\Gateway\Config\ValueHandlerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $tMapFactory = $this->getMockBuilder('Magento\Framework\ObjectManager\TMapFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $tMap = $this->getMockBuilder('Magento\Framework\ObjectManager\TMap')
            ->disableOriginalConstructor()
            ->getMock();

        $tMapFactory->expects(static::once())
            ->method('create')
            ->with(
                [
                    'array' => [
                        ValueHandlerPool::DEFAULT_HANDLER => 'Magento\Payment\Gateway\Config\ValueHandlerInterface',
                        'some_value' => 'Magento\Payment\Gateway\Config\ValueHandlerInterface'
                    ],
                    'type' => ValueHandlerInterface::class
                ]
            )
            ->willReturn($tMap);
        $tMap->expects(static::exactly(3))
            ->method('offsetExists')
            ->willReturnMap(
                [
                    [ValueHandlerPool::DEFAULT_HANDLER, true],
                    ['some_value', true]
                ]
            );
        $tMap->expects(static::exactly(3))
            ->method('offsetGet')
            ->willReturnMap(
                [
                    [ValueHandlerPool::DEFAULT_HANDLER, $defaultHandler],
                    ['some_value', $someValueHandler]
                ]
            );

        $pool = new ValueHandlerPool(
            $tMapFactory,
            [
                ValueHandlerPool::DEFAULT_HANDLER => 'Magento\Payment\Gateway\Config\ValueHandlerInterface',
                'some_value' => 'Magento\Payment\Gateway\Config\ValueHandlerInterface'
            ]
        );
        static::assertSame($someValueHandler, $pool->get('some_value'));
        static::assertSame($defaultHandler, $pool->get(ValueHandlerPool::DEFAULT_HANDLER));
        static::assertSame($defaultHandler, $pool->get('no_custom_logic_required'));
    }
}
