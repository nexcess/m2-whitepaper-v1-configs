<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Layer;

interface ContextInterface
{
    /**
     * @return ItemCollectionProviderInterface
     */
    public function getCollectionProvider();

    /**
     * @return StateKeyInterface
     */
    public function getStateKey();

    /**
     * @return CollectionFilterInterface
     */
    public function getCollectionFilter();
}
