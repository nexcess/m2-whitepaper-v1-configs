<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\User\Setup;

use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->upgradeHash($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeHash($setup)
    {
        $customerEntityTable = $setup->getTable('admin_user');

        $select = $setup->getConnection()->select()->from(
            $customerEntityTable,
            ['user_id', 'password']
        );

        $customers = $setup->getConnection()->fetchAll($select);
        foreach ($customers as $customer) {
            list($hash, $salt) = explode(Encryptor::DELIMITER, $customer['password']);

            $newHash = $customer['password'];
            if (strlen($hash) === 32) {
                $newHash = implode(Encryptor::DELIMITER, [$hash, $salt, Encryptor::HASH_VERSION_MD5]);
            } elseif (strlen($hash) === 64) {
                $newHash = implode(Encryptor::DELIMITER, [$hash, $salt, Encryptor::HASH_VERSION_SHA256]);
            }

            $bind = ['password' => $newHash];
            $where = ['user_id = ?' => (int)$customer['user_id']];
            $setup->getConnection()->update($customerEntityTable, $bind, $where);
        }
    }
}
