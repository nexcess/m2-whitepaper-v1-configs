<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Test\Unit\Model\Translate\Inline;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testIsActive()
    {
        $result = 'result';
        $backendConfig = $this->getMockForAbstractClass('Magento\Backend\App\ConfigInterface');
        $backendConfig->expects(
            $this->once()
        )->method(
            'isSetFlag'
        )->with(
            $this->equalTo('dev/translate_inline/active_admin')
        )->will(
            $this->returnValue($result)
        );
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $config = $objectManager->getObject(
            'Magento\Backend\Model\Translate\Inline\Config',
            ['config' => $backendConfig]
        );
        $this->assertEquals($result, $config->isActive('any'));
    }
}
