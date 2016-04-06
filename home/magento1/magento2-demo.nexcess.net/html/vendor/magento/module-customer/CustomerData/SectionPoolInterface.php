<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\CustomerData;

/**
 * Section pool interface
 */
interface SectionPoolInterface
{
    /**
     * Get section data by section names. If $sectionNames is null then return all sections data
     *
     * @param array $sectionNames
     * @param bool $updateIds
     * @return array
     */
    public function getSectionsData(array $sectionNames = null, $updateIds = false);
}
