<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Block;

use Magento\Customer\Block\Newsletter;

class NewsletterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var Newsletter
     */
    protected $block;

    protected function setUp()
    {
        $this->urlBuilder = $this->getMock('\Magento\Framework\UrlInterface');
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->block = $helper->getObject('Magento\Customer\Block\Newsletter', ['urlBuilder' => $this->urlBuilder]);
    }

    public function testGetAction()
    {
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('newsletter/manage/save', [])
            ->willReturn('newsletter/manage/save');

        $this->assertEquals('newsletter/manage/save', $this->block->getAction());
    }
}
