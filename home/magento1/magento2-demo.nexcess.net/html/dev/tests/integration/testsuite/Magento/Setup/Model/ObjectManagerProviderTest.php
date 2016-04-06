<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Magento\Setup\Mvc\Bootstrap\InitParamListener;

class ObjectManagerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerProvider
     */
    private $object;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $locator;

    protected function setUp()
    {
        $this->locator = $this->getMockForAbstractClass('Zend\ServiceManager\ServiceLocatorInterface');
        $this->object = new ObjectManagerProvider($this->locator);
    }

    public function testGet()
    {
        $this->locator->expects($this->once())->method('get')->with(InitParamListener::BOOTSTRAP_PARAM)->willReturn([]);
        $objectManager = $this->object->get();
        $this->assertInstanceOf('Magento\Framework\ObjectManagerInterface', $objectManager);
        $this->assertSame($objectManager, $this->object->get());
    }
}
