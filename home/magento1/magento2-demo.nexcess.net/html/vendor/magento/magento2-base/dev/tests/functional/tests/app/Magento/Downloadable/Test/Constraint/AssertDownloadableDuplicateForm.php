<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Downloadable\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductDuplicateForm;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Class AssertDownloadableDuplicateForm
 */
class AssertDownloadableDuplicateForm extends AssertProductDuplicateForm
{
    /**
     * Assert form data equals duplicate product downloadable data
     *
     * @param FixtureInterface $product
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductEdit $productPage
     * @return void
     */
    public function processAssert(
        FixtureInterface $product,
        CatalogProductIndex $productGrid,
        CatalogProductEdit $productPage
    ) {
        $filter = ['sku' => $product->getSku() . '-1'];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $formData = $productPage->getProductForm()->getData($product);
        $fixtureData = $this->convertDownloadableArray($this->prepareFixtureData($product->getData()));
        $errors = $this->verifyData($fixtureData, $formData);

        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Sort downloadable array
     *
     * @param array $fields
     * @return array
     */
    protected function sortDownloadableArray(array $fields)
    {
        usort(
            $fields,
            function ($row1, $row2) {
                if ($row1['sort_order'] == $row2['sort_order']) {
                    return 0;
                }

                return ($row1['sort_order'] < $row2['sort_order']) ? -1 : 1;
            }
        );

        return $fields;
    }

    /**
     * Convert fixture array
     *
     * @param array $fields
     * @return array
     */
    protected function convertDownloadableArray(array $fields)
    {
        if (isset($fields['downloadable_links']['downloadable']['link'])) {
            $fields['downloadable_links']['downloadable']['link'] = $this->sortDownloadableArray(
                $fields['downloadable_links']['downloadable']['link']
            );
        }
        if (isset($fields['downloadable_sample']['downloadable']['sample'])) {
            $fields['downloadable_sample']['downloadable']['sample'] = $this->sortDownloadableArray(
                $fields['downloadable_sample']['downloadable']['sample']
            );
        }

        return $fields;
    }
}
