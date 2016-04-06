<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Integration\Test\Unit\Model;

use Magento\Integration\Model\Config;
use Magento\Integration\Model\Cache\Type;

/**
 * Unit test for \Magento\Integration\Model\Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Integration config model
     *
     * @var Config
     */
    protected $configModel;

    /**
     * @var Type|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configCacheTypeMock;

    /**
     * @var  \Magento\Integration\Model\Config\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configReaderMock;

    public function setUp()
    {
        $this->configCacheTypeMock = $this->getMockBuilder('Magento\Integration\Model\Cache\Type')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configReaderMock = $this->getMockBuilder('Magento\Integration\Model\Config\Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configModel = new Config(
            $this->configCacheTypeMock,
            $this->configReaderMock
        );
    }

    public function testGetIntegrationsFromConfigCacheType()
    {
        $integrations = ['foo', 'bar', 'baz'];
        $this->configCacheTypeMock->expects($this->once())
            ->method('load')
            ->with(Config::CACHE_ID)
            ->will($this->returnValue(serialize($integrations)));

        $this->assertEquals($integrations, $this->configModel->getIntegrations());
    }

    public function testGetIntegrationsFromConfigReader()
    {
        $integrations = ['foo', 'bar', 'baz'];
        $this->configCacheTypeMock->expects($this->once())
            ->method('load')
            ->with(Config::CACHE_ID)
            ->will($this->returnValue(null));
        $this->configCacheTypeMock->expects($this->once())
            ->method('save')
            ->with(serialize($integrations), Config::CACHE_ID, [Type::CACHE_TAG])
            ->will($this->returnValue(null));
        $this->configReaderMock->expects($this->once())
            ->method('read')
            ->will($this->returnValue($integrations));

        $this->assertEquals($integrations, $this->configModel->getIntegrations());
    }
}
