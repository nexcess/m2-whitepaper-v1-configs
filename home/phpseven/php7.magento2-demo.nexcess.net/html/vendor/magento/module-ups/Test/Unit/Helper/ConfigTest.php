<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ups\Test\Unit\Helper;

/**
 * Config helper Test
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ups config helper
     *
     * @var \Magento\Ups\Helper\Config
     */
    protected $helper;

    public function setUp()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->helper = $objectManagerHelper->getObject('Magento\Ups\Helper\Config');
    }

    /**
     * @param mixed $result
     * @param null|string $type
     * @param string $code
     * @dataProvider getCodeDataProvider
     */
    public function testGetData($result, $type = null, $code = null)
    {
        $this->assertEquals($result, $this->helper->getCode($type, $code));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getCodeDataProvider()
    {
        return [
            [false],
            [false, 'not-exist-type'],
            [false, 'not-exist-type', 'not-exist-code'],
            [false, 'action'],
            [['single' => '3', 'all' => '4'], 'action', ''],
            ['3', 'action', 'single']
        ];
    }
}
