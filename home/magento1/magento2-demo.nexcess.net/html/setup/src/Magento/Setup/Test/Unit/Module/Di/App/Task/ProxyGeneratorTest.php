<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Test\Unit\Module\Di\App\Task;

use Magento\Setup\Module\Di\App\Task\Operation\ProxyGenerator;
use Magento\Setup\Module\Di\Code\Scanner;
use Magento\Setup\Module\Di\Code\Reader\ClassesScanner;

class ProxyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Scanner\XmlScanner | \PHPUnit_Framework_MockObject_MockObject
     */
    private $proxyScannerMock;

    /**
     * @var \Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner | \PHPUnit_Framework_MockObject_MockObject
     */
    private $configurationScannerMock;

    /**
     * @var \Magento\Setup\Module\Di\App\Task\Operation\ProxyGenerator
     */
    private $model;

    protected function setUp()
    {
        $this->proxyScannerMock = $this->getMockBuilder('Magento\Setup\Module\Di\Code\Scanner\XmlScanner')
            ->disableOriginalConstructor()
            ->getMock();

        $this->configurationScannerMock = $this->getMockBuilder(
            'Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner'
        )->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\Setup\Module\Di\App\Task\Operation\ProxyGenerator',
            [
                'proxyScanner' => $this->proxyScannerMock,
                'configurationScanner' => $this->configurationScannerMock,
            ]
        );
    }

    public function testDoOperation()
    {
        $files = ['file1', 'file2'];
        $this->configurationScannerMock->expects($this->once())
            ->method('scan')
            ->with('di.xml')
            ->willReturn($files);
        $this->proxyScannerMock->expects($this->once())
            ->method('collectEntities')
            ->with($files)
            ->willReturn([]);

        $this->model->doOperation();
    }
}
