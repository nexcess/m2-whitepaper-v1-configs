<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reports\Block\Adminhtml\Shopcart\Abandoned;

use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Magento\Reports\Block\Adminhtml\Shopcart\Abandoned\Grid
 *
 * @magentoAppArea adminhtml
 * @magentoDataFixture Magento/Sales/_files/quote.php
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
class GridTest extends \Magento\Reports\Block\Adminhtml\Shopcart\GridTestAbstract
{
    /**
     * @return void
     */
    public function testGridContent()
    {
        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get('Magento\Framework\View\LayoutInterface');
        /** @var Grid $grid */
        $grid = $layout->createBlock('Magento\Reports\Block\Adminhtml\Shopcart\Abandoned\Grid');
        $grid->getRequest()->setParams(['filter' => base64_encode(urlencode('email=customer@example.com'))]);
        $result = $grid->getPreparedCollection();

        $this->assertCount(1, $result->getItems());
        /** @var Quote $quote */
        $quote = $result->getFirstItem();
        $this->assertEquals('customer@example.com', $quote->getCustomerEmail());
        $this->assertEquals(10.00, $quote->getSubtotal());
    }
}
