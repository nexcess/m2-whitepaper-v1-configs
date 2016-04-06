<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Indexer\Handler;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\Indexer\HandlerInterface;

class AttributeHandler implements HandlerInterface
{
    /**
     * Prepare SQL for field and add it to collection
     *
     * @param SourceProviderInterface $source
     * @param string $alias
     * @param array $fieldInfo
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepareSql(SourceProviderInterface $source, $alias, $fieldInfo)
    {
        if (isset($fieldInfo['bind'])) {
            $source->joinAttribute(
                $fieldInfo['name'],
                $fieldInfo['entity'] . '/' . $fieldInfo['origin'],
                $fieldInfo['bind'],
                null,
                'left'
            );
        } else {
            $source->addAttributeToSelect($fieldInfo['origin'], 'left');
        }
    }
}
