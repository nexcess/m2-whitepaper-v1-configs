<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdminNotification\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FeedTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\AdminNotification\Model\Feed */
    protected $feed;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\AdminNotification\Model\InboxFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $inboxFactory;

    /** @var \Magento\AdminNotification\Model\Inbox|\PHPUnit_Framework_MockObject_MockObject */
    protected $inboxModel;

    /** @var \Magento\Framework\HTTP\Adapter\CurlFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $curlFactory;

    /** @var \Magento\Framework\HTTP\Adapter\Curl|\PHPUnit_Framework_MockObject_MockObject */
    protected $curl;

    /** @var \Magento\Backend\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $backendConfig;

    /** @var \Magento\Framework\App\CacheInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $cacheManager;

    /** @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject */
    protected $appState;

    /** @var \Magento\Framework\App\DeploymentConfig|\PHPUnit_Framework_MockObject_MockObject */
    protected $deploymentConfig;

    /** @var \Magento\Framework\App\ProductMetadata|\PHPUnit_Framework_MockObject_MockObject */
    protected $productMetadata;

    /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlBuilder;

    protected function setUp()
    {
        $this->inboxFactory = $this->getMock('Magento\AdminNotification\Model\InboxFactory', ['create'], [], '', false);
        $this->curlFactory = $this->getMock('Magento\Framework\HTTP\Adapter\CurlFactory', ['create'], [], '', false);
        $this->curl = $this->getMockBuilder('Magento\Framework\HTTP\Adapter\Curl')
            ->disableOriginalConstructor()->getMock();
        $this->appState = $this->getMock('Magento\Framework\App\State', ['getInstallDate'], [], '', false);
        $this->inboxModel = $this->getMock(
            'Magento\AdminNotification\Model\Inbox',
            [
                '__wakeup',
                'parse'
            ],
            [],
            '',
            false
        );
        $this->backendConfig = $this->getMock(
            'Magento\Backend\App\ConfigInterface',
            [
                'getValue',
                'setValue',
                'isSetFlag'
            ]
        );
        $this->cacheManager = $this->getMock(
            'Magento\Framework\App\CacheInterface',
            [
                'load',
                'getFrontend',
                'remove',
                'save',
                'clean'
            ]
        );

        $this->deploymentConfig = $this->getMockBuilder('Magento\Framework\App\DeploymentConfig')
            ->disableOriginalConstructor()->getMock();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->productMetadata = $this->getMock('Magento\Framework\App\ProductMetadata');
        $this->urlBuilder = $this->getMock('Magento\Framework\UrlInterface');

        $this->feed = $this->objectManagerHelper->getObject(
            'Magento\AdminNotification\Model\Feed',
            [
                'backendConfig' => $this->backendConfig,
                'cacheManager' => $this->cacheManager,
                'inboxFactory' => $this->inboxFactory,
                'appState' => $this->appState,
                'curlFactory' => $this->curlFactory,
                'deploymentConfig' => $this->deploymentConfig,
                'productMetadata' => $this->productMetadata,
                'urlBuilder' => $this->urlBuilder
            ]
        );
    }

    /**
     * @dataProvider checkUpdateDataProvider
     * @param bool $callInbox
     * @param string $curlRequest
     */
    public function testCheckUpdate($callInbox, $curlRequest)
    {
        $mockName    = 'Test Product Name';
        $mockVersion = '0.0.0';
        $mockEdition = 'Test Edition';
        $mockUrl = 'http://test-url';

        $this->productMetadata->expects($this->once())->method('getName')->willReturn($mockName);
        $this->productMetadata->expects($this->once())->method('getVersion')->willReturn($mockVersion);
        $this->productMetadata->expects($this->once())->method('getEdition')->willReturn($mockEdition);
        $this->urlBuilder->expects($this->once())->method('getUrl')->with('*/*/*')->willReturn($mockUrl);

        $configValues = [
            'timeout'   => 2,
            'useragent' => $mockName . '/' . $mockVersion . ' (' . $mockEdition . ')',
            'referer'   => $mockUrl
        ];

        $lastUpdate = 0;
        $this->cacheManager->expects($this->once())->method('load')->will(($this->returnValue($lastUpdate)));
        $this->curlFactory->expects($this->at(0))->method('create')->will($this->returnValue($this->curl));
        $this->curl->expects($this->once())->method('setConfig')->with($configValues)->willReturnSelf();
        $this->curl->expects($this->once())->method('read')->will($this->returnValue($curlRequest));
        $this->backendConfig->expects($this->at(0))->method('getValue')->will($this->returnValue('1'));
        $this->backendConfig->expects($this->once())->method('isSetFlag')->will($this->returnValue(false));
        $this->backendConfig->expects($this->at(1))->method('getValue')
            ->will($this->returnValue('http://feed.magento.com'));
        $this->deploymentConfig->expects($this->once())->method('get')
            ->with(ConfigOptionsListConstants::CONFIG_PATH_INSTALL_DATE)
            ->will($this->returnValue('Sat, 6 Sep 2014 16:46:11 UTC'));
        if ($callInbox) {
            $this->inboxFactory->expects($this->once())->method('create')
                ->will(($this->returnValue($this->inboxModel)));
            $this->inboxModel->expects($this->once())->method('parse')->will($this->returnSelf());
        } else {
            $this->inboxFactory->expects($this->never())->method('create');
            $this->inboxModel->expects($this->never())->method('parse');
        }

        $this->feed->checkUpdate();
    }

    /**
     * @return array
     */
    public function checkUpdateDataProvider()
    {
        return [
            [
                true,
                'HEADER

                <?xml version="1.0" encoding="utf-8" ?>
                        <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
                            <channel>
                                <title>MagentoCommerce</title>
                                <item>
                                    <title><![CDATA[Test Title]]></title>
                                    <link><![CDATA[http://magento.com/feed_url]]></link>
                                    <severity>4</severity>
                                    <description><![CDATA[Test Description]]></description>
                                    <pubDate>Tue, 9 Sep 2014 16:46:11 UTC</pubDate>
                                </item>
                            </channel>
                        </rss>',
            ],
            [
                false,
                'HEADER

                <?xml version="1.0" encoding="utf-8" ?>
                        <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
                            <channel>
                                <title>MagentoCommerce</title>
                                <item>
                                    <title><![CDATA[Test Title]]></title>
                                    <link><![CDATA[http://magento.com/feed_url]]></link>
                                    <severity>4</severity>
                                    <description><![CDATA[Test Description]]></description>
                                    <pubDate>Tue, 1 Sep 2014 16:46:11 UTC</pubDate>
                                </item>
                            </channel>
                        </rss>'
            ]
        ];
    }
}
