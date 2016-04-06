<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Test\Unit\App;

class UserConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testUserRequestCreation()
    {
        $factoryMock = $this->getMock('Magento\Config\Model\Config\Factory', ['create'], [], '', false);
        $responseMock = $this->getMock('Magento\Framework\App\Console\Response', [], [], '', false);
        $configMock = $this->getMock('Magento\Config\Model\Config', [], [], '', false);

        $key = 'key';
        $value = 'value';
        $request = [$key => $value];
        $model = new \Magento\Backend\App\UserConfig($factoryMock, $responseMock, $request);
        $factoryMock->expects($this->once())->method('create')->will($this->returnValue($configMock));
        $configMock->expects($this->once())->method('setDataByPath')->with($key, $value);
        $configMock->expects($this->once())->method('save');

        $model->launch();
    }
}
