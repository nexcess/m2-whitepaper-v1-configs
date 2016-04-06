<?php
/**
 * Magento application product metadata
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App;

use Magento\Framework\AppInterface;

class ProductMetadata implements ProductMetadataInterface
{
    const EDITION_NAME  = 'Community';
    const PRODUCT_NAME  = 'Magento';

    /**
     * Get Product version
     *
     * @return string
     */
    public function getVersion()
    {
        return AppInterface::VERSION;
    }

    /**
     * Get Product edition
     *
     * @return string
     */
    public function getEdition()
    {
        return self::EDITION_NAME;
    }

    /**
     * Get Product name
     *
     * @return string
     */
    public function getName()
    {
        return self::PRODUCT_NAME;
    }
}
