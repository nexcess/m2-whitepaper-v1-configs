<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Translate\Test\Unit;

class AdapterAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Translate\AbstractAdapter
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = $this->getMockBuilder('Magento\Framework\Translate\AbstractAdapter')->getMockForAbstractClass();
    }

    /**
     * Magento translate adapter should always return false to be used correctly be Zend Validate
     */
    public function testIsTranslated()
    {
        $this->assertFalse($this->_model->isTranslated('string'));
    }

    /**
     * Test set locale do nothing
     */
    public function testSetLocale()
    {
        $this->assertInstanceOf('Magento\Framework\Translate\AbstractAdapter', $this->_model->setLocale('en_US'));
    }

    /**
     * Check that abstract method is implemented
     */
    public function testToString()
    {
        $this->assertEquals('Magento\Framework\Translate\Adapter', $this->_model->toString());
    }
}
