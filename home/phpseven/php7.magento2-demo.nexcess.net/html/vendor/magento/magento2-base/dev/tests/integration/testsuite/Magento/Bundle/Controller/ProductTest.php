<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Catalog\Controller\Product (bundle product type)
 */
namespace Magento\Bundle\Controller;

class ProductTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/Bundle/_files/product.php
     */
    public function testViewAction()
    {
        $this->dispatch('catalog/product/view/id/3');
        $responseBody = $this->getResponse()->getBody();
        $this->assertContains('Bundle Product', $responseBody);
        $this->assertContains(
            'In stock',
            $responseBody,
            'Bundle Product Detailed Page does not contain In Stock field'
        );
        $addToCartCount = substr_count($responseBody, '<span>Add to Cart</span>');
        $this->assertEquals(1, $addToCartCount, '"Add to Cart" button should appear on the page exactly once.');
        $actualLinkCount = substr_count($responseBody, '>Bundle Product Items<');
        $this->assertEquals(1, $actualLinkCount, 'Bundle product options should appear on the page exactly once.');
        $this->assertNotContains('class="options-container-big"', $responseBody);
        $this->assertSelectCount('#product-options-wrapper', 1, $responseBody);
    }
}
