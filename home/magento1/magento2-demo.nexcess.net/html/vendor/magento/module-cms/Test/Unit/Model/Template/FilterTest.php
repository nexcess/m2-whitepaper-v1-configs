<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cms\Test\Unit\Model\Template;

/**
 * @covers \Magento\Cms\Model\Template\Filter
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Cms\Model\Template\Filter
     */
    protected $filter;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->filter = $objectManager->getObject(
            'Magento\Cms\Model\Template\Filter',
            ['storeManager' => $this->storeManagerMock]
        );
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($this->storeMock);
    }

    /**
     * @covers \Magento\Cms\Model\Template\Filter::mediaDirective
     */
    public function testMediaDirective()
    {
        $baseMediaDir = 'pub/media';
        $construction = [
            '{{media url="wysiwyg/image.jpg"}}',
            'media',
            ' url="wysiwyg/image.jpg"'
        ];
        $expectedResult = 'pub/media/wysiwyg/image.jpg';
        $this->storeMock->expects($this->once())
            ->method('getBaseMediaDir')
            ->willReturn($baseMediaDir);
        $this->assertEquals($expectedResult, $this->filter->mediaDirective($construction));
    }
}
