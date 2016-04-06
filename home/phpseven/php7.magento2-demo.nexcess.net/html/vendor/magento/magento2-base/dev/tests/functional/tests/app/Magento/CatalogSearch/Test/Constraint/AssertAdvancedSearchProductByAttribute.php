<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CatalogSearch\Test\Page\AdvancedSearch;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Page\CatalogsearchResult;

/**
 * Assert that product attribute is searchable on Frontend.
 */
class AssertAdvancedSearchProductByAttribute extends AbstractConstraint
{
    /**
     * Factory for fixtures.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Assert that product attribute is searchable on Frontend.
     *
     * @param CmsIndex $cmsIndex
     * @param InjectableFixture $product
     * @param AdvancedSearch $searchPage
     * @param CatalogsearchResult $catalogSearchResult
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        InjectableFixture $product,
        AdvancedSearch $searchPage,
        CatalogsearchResult $catalogSearchResult,
        FixtureFactory $fixtureFactory
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $cmsIndex->open();
        $cmsIndex->getFooterBlock()->openAdvancedSearch();
        $searchForm = $searchPage->getForm();
        $productSearch = $this->prepareFixture($product);

        $searchForm->fill($productSearch);
        $searchForm->submit();
        $isVisible = $catalogSearchResult->getListProductBlock()->getProductItem($product)->isVisible();
        while (!$isVisible && $catalogSearchResult->getBottomToolbar()->nextPage()) {
            $isVisible = $catalogSearchResult->getListProductBlock()->getProductItem($product)->isVisible();
        }

        \PHPUnit_Framework_Assert::assertTrue($isVisible, 'Product attribute is not searchable on Frontend.');
    }

    /**
     * Preparation of fixture data before comparing.
     *
     * @param InjectableFixture $productSearch
     * @return CatalogProductSimple
     */
    protected function prepareFixture(InjectableFixture $productSearch)
    {
        $customAttribute = $productSearch->getDataFieldConfig('custom_attribute')['source']->getAttribute();
        return $this->fixtureFactory->createByCode(
            'catalogProductSimple',
            ['data' => ['custom_attribute' => $customAttribute]]
        );
    }

    /**
     * Returns string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product attribute is searchable on Frontend.';
    }
}
