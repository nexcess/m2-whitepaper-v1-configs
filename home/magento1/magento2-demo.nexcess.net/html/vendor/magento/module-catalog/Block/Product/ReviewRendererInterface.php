<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Product;

interface ReviewRendererInterface
{
    const SHORT_VIEW = 'short';
    const FULL_VIEW = 'default';
    const DEFAULT_VIEW = self::FULL_VIEW;

    /**
     * Get product review summary html
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = self::DEFAULT_VIEW,
        $displayIfNoReviews = false
    );
}
