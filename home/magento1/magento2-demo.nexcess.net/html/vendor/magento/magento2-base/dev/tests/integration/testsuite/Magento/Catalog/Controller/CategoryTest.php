<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Controller;

/**
 * Test class for \Magento\Catalog\Controller\Category.
 *
 * @magentoAppArea frontend
 */
class CategoryTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function assert404NotFound()
    {
        parent::assert404NotFound();
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->assertNull($objectManager->get('Magento\Framework\Registry')->registry('current_category'));
    }

    public function getViewActionDataProvider()
    {
        return [
            'category without children' => [
                'categoryId' => 5,
                ['catalog_category_view_type_layered', 'catalog_category_view_type_layered_without_children'],
                [
                    '%acategorypath-category-1-category-1-1-category-1-1-1%a',
                    '%acategory-category-1-1-1%a',
                    '%a<title>Category 1.1.1 - Category 1.1 - Category 1</title>%a',
                    '%a<h1%a>%SCategory 1.1.1%S</h1>%a',
                    '%aSimple Product Two%a',
                    '%a$45.67%a'
                ],
            ],
            'anchor category' => [
                'categoryId' => 4,
                ['catalog_category_view_type_layered'],
                [
                    '%acategorypath-category-1-category-1-1%a',
                    '%acategory-category-1-1%a',
                    '%a<title>Category 1.1 - Category 1</title>%a',
                    '%a<h1%a>%SCategory 1.1%S</h1>%a',
                    '%aSimple Product%a',
                    '%a$10.00%a',
                    '%aSimple Product Two%a',
                    '%a$45.67%a'
                ],
            ]
        ];
    }

    /**
     * @dataProvider getViewActionDataProvider
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/categories_with_products.php
     */
    public function testViewAction($categoryId, array $expectedHandles, array $expectedContent)
    {
        $this->dispatch("catalog/category/view/id/{$categoryId}");

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $currentCategory \Magento\Catalog\Model\Category */
        $currentCategory = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
        $this->assertInstanceOf('Magento\Catalog\Model\Category', $currentCategory);
        $this->assertEquals($categoryId, $currentCategory->getId(), 'Category in registry.');

        $lastCategoryId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Catalog\Model\Session'
        )->getLastVisitedCategoryId();
        $this->assertEquals($categoryId, $lastCategoryId, 'Last visited category.');

        /* Layout updates */
        $handles = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        )->getUpdate()->getHandles();
        foreach ($expectedHandles as $expectedHandleName) {
            $this->assertContains($expectedHandleName, $handles);
        }

        $responseBody = $this->getResponse()->getBody();

        /* Response content */
        foreach ($expectedContent as $expectedText) {
            $this->assertStringMatchesFormat($expectedText, $responseBody);
        }
    }

    public function testViewActionNoCategoryId()
    {
        $this->dispatch('catalog/category/view/');

        $this->assert404NotFound();
    }

    public function testViewActionInactiveCategory()
    {
        $this->dispatch('catalog/category/view/id/8');

        $this->assert404NotFound();
    }
}
