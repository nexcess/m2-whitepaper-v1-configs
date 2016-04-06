<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Test\Unit\Model\Indexer\Product\Price\Plugin;

class WebsiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Plugin\Website
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_priceProcessorMock;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->_priceProcessorMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Price\Processor',
            ['markIndexerAsInvalid'],
            [],
            '',
            false
        );

        $this->_model = $this->_objectManager->getObject(
            'Magento\Catalog\Model\Indexer\Product\Price\Plugin\Website',
            ['processor' => $this->_priceProcessorMock]
        );
    }

    public function testAfterDelete()
    {
        $this->_priceProcessorMock->expects($this->once())->method('markIndexerAsInvalid');

        $websiteMock = $this->getMock('Magento\Store\Model\ResourceModel\Website', [], [], '', false);
        $this->assertEquals('return_value', $this->_model->afterDelete($websiteMock, 'return_value'));
    }
}
