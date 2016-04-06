<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

class SearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     */
    public function testToHtmlHasIndex()
    {
        /** @var $layout \Magento\Framework\View\LayoutInterface */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Framework\View\Layout');
        $block = $layout->createBlock(
            'Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search',
            'block2'
        );

        $indexValue = 'magento_index_set_to_test';
        $block->setIndex($indexValue);

        $html = $block->toHtml();
        $this->assertContains($indexValue, $html);
    }
}
