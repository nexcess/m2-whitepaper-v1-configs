<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\Test\Unit\ObjectManager\Environment;

use Magento\Framework\App\ObjectManager\Environment\Compiled;

class CompiledTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Compiled
     */
    protected $_compiled;

    protected function setUp()
    {
        $envFactoryMock = $this->getMock('Magento\Framework\App\EnvironmentFactory', [], [], '', false);
        $this->_compiled = new CompiledTesting($envFactoryMock);
    }

    public function testGetMode()
    {
        $this->assertEquals(Compiled::MODE, $this->_compiled->getMode());
    }

    public function testGetObjectManagerFactory()
    {
        $this->assertInstanceOf(
            'Magento\Framework\ObjectManager\Factory\Compiled',
            $this->_compiled->getObjectManagerFactory(['shared_instances' => []])
        );
    }
}
