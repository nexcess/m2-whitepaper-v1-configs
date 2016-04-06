<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Block;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Class Search
 * Block for "Search" section
 */
class Search extends Block
{
    /**
     * Locator value for matches found - "Suggest Search".
     *
     * @var string
     */
    protected $searchAutocomplete = './/div[@id="search_autocomplete"]//li[span[text()[normalize-space()="%s"]]]';

    /**
     * Locator value for given row matches amount.
     *
     * @var string
     */
    protected $searchItemAmount = '/span[contains(@class,"amount") and text()="%d"]';

    /**
     * Locator value for "Search" field.
     *
     * @var string
     */
    protected $searchInput = '#search';

    /**
     * Locator value for "Search" button.
     *
     * @var string
     */
    private $searchButton = '[title="Search"]';

    /**
     * Locator value for "Search" button placeholder.
     *
     * @var string
     */
    protected $placeholder = '//input[@id="search" and contains(@placeholder, "%s")]';

    /**
     * Perform search by a keyword.
     *
     * @param string $keyword
     * @return void
     *
     * @SuppressWarnings(PHPMD.ConstructorWithNameAsEnclosingClass)
     */
    public function search($keyword)
    {
        $this->fillSearch($keyword);
        $this->_rootElement->find($this->searchButton)->click();
    }

    /**
     * Fill "Search" field with correspondent text.
     *
     * @param string $text
     * @return void
     */
    public function fillSearch($text)
    {
        $this->_rootElement->find($this->searchInput)->setValue($text);
    }

    /**
     * Check if placeholder contains correspondent text or not.
     *
     * @param string $placeholderText
     * @return bool
     */
    public function isPlaceholderContains($placeholderText)
    {
        $field = $this->_rootElement->find(sprintf($this->placeholder, $placeholderText), Locator::SELECTOR_XPATH);
        return $field->isVisible();
    }

    /**
     * Check if "Suggest Search" block visible or not.
     *
     * @param string $text
     * @param int|null $amount
     * @return bool
     */
    public function isSuggestSearchVisible($text, $amount = null)
    {
        $searchAutocomplete = sprintf($this->searchAutocomplete, $text);
        if ($amount !== null) {
            $searchAutocomplete .= sprintf($this->searchItemAmount, $amount);
        }

        $rootElement = $this->_rootElement;
        return (bool)$this->_rootElement->waitUntil(
            function () use ($rootElement, $searchAutocomplete) {
                return $rootElement->find($searchAutocomplete, Locator::SELECTOR_XPATH)->isVisible() ? true : null;
            }
        );
    }
}
