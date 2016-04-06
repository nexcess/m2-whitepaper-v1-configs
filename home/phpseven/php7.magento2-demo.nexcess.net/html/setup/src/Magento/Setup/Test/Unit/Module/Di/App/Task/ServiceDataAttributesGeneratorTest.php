<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Test\Unit\Module\Di\App\Task;

use Magento\Setup\Module\Di\Code\Scanner;

/**
 * Class ServiceDataAttributesGeneratorTest
 */
class ServiceDataAttributesGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner | \PHPUnit_Framework_MockObject_MockObject
     */
    private $configurationScannerMock;

    /**
     * @var \Magento\Setup\Module\Di\Code\Scanner\ServiceDataAttributesScanner|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceDataAttributesScannerMock;

    /**
     * @var \Magento\Setup\Module\Di\App\Task\Operation\ServiceDataAttributesGenerator
     */
    private $model;

    protected function setUp()
    {
        $this->configurationScannerMock = $this->getMockBuilder(
            'Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner'
        )->disableOriginalConstructor()
            ->getMock();
        $this->serviceDataAttributesScannerMock = $this->getMockBuilder(
            'Magento\Setup\Module\Di\Code\Scanner\ServiceDataAttributesScanner'
        )->disableOriginalConstructor()
            ->getMock();
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\Setup\Module\Di\App\Task\Operation\ServiceDataAttributesGenerator',
            [
                'serviceDataAttributesScanner' => $this->serviceDataAttributesScannerMock,
                'configurationScanner' => $this->configurationScannerMock,
            ]
        );
    }

    public function testDoOperation()
    {
        $files = ['file1', 'file2'];
        $this->configurationScannerMock->expects($this->once())
            ->method('scan')
            ->with('extension_attributes.xml')
            ->willReturn($files);
        $this->serviceDataAttributesScannerMock->expects($this->once())
            ->method('collectEntities')
            ->with($files)
            ->willReturn([]);

        $this->model->doOperation();
    }
}
