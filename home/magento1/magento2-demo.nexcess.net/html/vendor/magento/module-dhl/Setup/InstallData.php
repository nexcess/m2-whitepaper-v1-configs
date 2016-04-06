<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Dhl\Setup;

use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Locale list
     *
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * Init
     *
     * @param ResolverInterface $localeResolver
     */
    public function __construct(ResolverInterface $localeResolver)
    {
        $this->localeResolver = $localeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $days = (new DataBundle())->get(
            $this->localeResolver->getLocale()
        )['calendar']['gregorian']['dayNames']['format']['abbreviated'];

        $select = $setup->getConnection()->select()->from(
            $setup->getTable('core_config_data'),
            ['config_id', 'value']
        )->where(
            'path = ?',
            'carriers/dhl/shipment_days'
        );

        foreach ($setup->getConnection()->fetchAll($select) as $configRow) {
            $row = [
                'value' => implode(
                    ',',
                    array_intersect_key(iterator_to_array($days), array_flip(explode(',', $configRow['value'])))
                )
            ];
            $setup->getConnection()->update(
                $setup->getTable('core_config_data'),
                $row,
                ['config_id = ?' => $configRow['config_id']]
            );
        }
    }
}
