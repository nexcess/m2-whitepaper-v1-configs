<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Test\Unit\Block\Adminhtml;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class RssTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Review\Block\Adminhtml\Rss
     */
    protected $block;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerInterface;

    /**
     * @var \Magento\Review\Model\Rss|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rss;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    protected function setUp()
    {
        $this->storeManagerInterface = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->rss = $this->getMock('Magento\Review\Model\Rss', ['__wakeUp', 'getProductCollection'], [], '', false);
        $this->urlBuilder = $this->getMock('Magento\Framework\UrlInterface');
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $this->objectManagerHelper->getObject(
            'Magento\Review\Block\Adminhtml\Rss',
            [
                'storeManager' => $this->storeManagerInterface,
                'rssModel' => $this->rss,
                'urlBuilder' => $this->urlBuilder,
            ]
        );
    }

    public function testGetRssData()
    {
        $rssData = [
            'title' => 'Pending product review(s)',
            'description' => 'Pending product review(s)',
            'link' => 'http://rss.magento.com',
            'charset' => 'UTF-8',
            'entries' => [
                    'title' => 'Product: "Product Name" reviewed by: Product Nick',
                    'link' => 'http://product.magento.com',
                    'description' => [
                            'rss_url' => 'http://rss.magento.com',
                            'name' => 'Product Name',
                            'summary' => 'Product Title',
                            'review' => 'Product Detail',
                            'store' => 'Store Name',

                        ],
                ],
        ];
        $rssUrl = 'http://rss.magento.com';
        $productModel = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Product',
            [
                'getStoreId',
                'getId',
                'getReviewId',
                'getName',
                'getDetail',
                'getTitle',
                'getNickname',
                'getProductUrl'
            ],
            [],
            '',
            false
        );
        $storeModel = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerInterface->expects($this->once())->method('getStore')->will($this->returnValue($storeModel));
        $storeModel->expects($this->once())->method('getName')
            ->will($this->returnValue($rssData['entries']['description']['store']));
        $this->urlBuilder->expects($this->any())->method('getUrl')->will($this->returnValue($rssUrl));
        $this->urlBuilder->expects($this->once())->method('setScope')->will($this->returnSelf());
        $productModel->expects($this->any())->method('getStoreId')->will($this->returnValue(1));
        $productModel->expects($this->any())->method('getId')->will($this->returnValue(1));
        $productModel->expects($this->once())->method('getReviewId')->will($this->returnValue(1));
        $productModel->expects($this->any())->method('getNickName')->will($this->returnValue('Product Nick'));
        $productModel->expects($this->any())->method('getName')
            ->will($this->returnValue($rssData['entries']['description']['name']));
        $productModel->expects($this->once())->method('getDetail')
            ->will($this->returnValue($rssData['entries']['description']['review']));
        $productModel->expects($this->once())->method('getTitle')
            ->will($this->returnValue($rssData['entries']['description']['summary']));
        $productModel->expects($this->any())->method('getProductUrl')
            ->will($this->returnValue('http://product.magento.com'));
        $this->rss->expects($this->once())->method('getProductCollection')
            ->will($this->returnValue([$productModel]));

        $data = $this->block->getRssData();

        $this->assertEquals($rssData['title'], $data['title']);
        $this->assertEquals($rssData['description'], $data['description']);
        $this->assertEquals($rssData['link'], $data['link']);
        $this->assertEquals($rssData['charset'], $data['charset']);
        $this->assertEquals($rssData['entries']['title'], $data['entries'][0]['title']);
        $this->assertEquals($rssData['entries']['link'], $data['entries'][0]['link']);
        $this->assertContains($rssData['entries']['description']['rss_url'], $data['entries'][0]['description']);
        $this->assertContains($rssData['entries']['description']['name'], $data['entries'][0]['description']);
        $this->assertContains($rssData['entries']['description']['summary'], $data['entries'][0]['description']);
        $this->assertContains($rssData['entries']['description']['review'], $data['entries'][0]['description']);
        $this->assertContains($rssData['entries']['description']['store'], $data['entries'][0]['description']);
    }

    public function testGetCacheLifetime()
    {
        $this->assertEquals(0, $this->block->getCacheLifetime());
    }

    public function testIsAllowed()
    {
        $this->assertEquals(true, $this->block->isAllowed());
    }

    public function testGetFeeds()
    {
        $this->assertEquals([], $this->block->getFeeds());
    }
}
