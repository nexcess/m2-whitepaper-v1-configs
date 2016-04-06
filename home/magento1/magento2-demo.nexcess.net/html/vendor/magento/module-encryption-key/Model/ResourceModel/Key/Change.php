<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\EncryptionKey\Model\ResourceModel\Key;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Config\Data\ConfigData;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\App\ObjectManager;

/**
 * Encryption key changer resource model
 * The operation must be done in one transaction
 */
class Change extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Encryptor interface
     *
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * Filesystem directory write interface
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * System configuration structure
     *
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $structure;

    /**
     * Configuration writer
     *
     * @var \Magento\Framework\App\DeploymentConfig\Writer
     */
    protected $writer;

    /**
     * Random
     *
     * @var \Magento\Framework\Math\Random
     */
    protected $random;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Config\Model\Config\Structure $structure
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\App\DeploymentConfig\Writer $writer
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Config\Model\Config\Structure $structure,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\App\DeploymentConfig\Writer $writer,
        $connectionName = null
    ) {
        $this->encryptor = clone $encryptor;
        parent::__construct($context, $connectionName);
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::CONFIG);
        $this->structure = $structure;
        $this->writer = $writer;
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('core_config_data', 'config_id');
    }

    /**
     * Change encryption key
     *
     * @param string|null $key
     * @return null|string
     * @throws \Exception
     */
    public function changeEncryptionKey($key = null)
    {
        // prepare new key, encryptor and new configuration segment
        if (!$this->writer->checkIfWritable()) {
            throw new \Exception(__('Deployment configuration file is not writable.'));
        }

        if (null === $key) {
            $key = md5($this->getRandom()->getRandomString(ConfigOptionsListConstants::STORE_KEY_RANDOM_STRING_SIZE));
        }
        $this->encryptor->setNewKey($key);

        $encryptSegment = new ConfigData(ConfigFilePool::APP_ENV);
        $encryptSegment->set(ConfigOptionsListConstants::CONFIG_PATH_CRYPT_KEY, $this->encryptor->exportKeys());

        $configData = [$encryptSegment->getFileKey() => $encryptSegment->getData()];

        // update database and config.php
        $this->beginTransaction();
        try {
            $this->_reEncryptSystemConfigurationValues();
            $this->_reEncryptCreditCardNumbers();
            $this->writer->saveConfig($configData);
            $this->commit();
            return $key;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Get Math Random
     *
     * @return \Magento\Framework\Math\Random
     */
    public function getRandom()
    {
        if (!$this->random) {
            $this->random = ObjectManager::getInstance()->get('\Magento\Framework\Math\Random');
        }
        return $this->random;
    }

    /**
     * Set Random
     *
     * @param \Magento\Framework\Math\Random $random
     */
    public function setRandom(\Magento\Framework\Math\Random $random)
    {
        $this->random = $random;
    }

    /**
     * Gather all encrypted system config values and re-encrypt them
     *
     * @return void
     */
    protected function _reEncryptSystemConfigurationValues()
    {
        // look for encrypted node entries in all system.xml files
        /** @var \Magento\Config\Model\Config\Structure $configStructure  */
        $configStructure = $this->structure;
        $paths = $configStructure->getFieldPathsByAttribute(
            'backend_model',
            'Magento\Config\Model\Config\Backend\Encrypted'
        );

        // walk through found data and re-encrypt it
        if ($paths) {
            $table = $this->getTable('core_config_data');
            $values = $this->getConnection()->fetchPairs(
                $this->getConnection()
                    ->select()
                    ->from($table, ['config_id', 'value'])
                    ->where('path IN (?)', $paths)
                    ->where('value NOT LIKE ?', '')
            );
            foreach ($values as $configId => $value) {
                $this->getConnection()->update(
                    $table,
                    ['value' => $this->encryptor->encrypt($this->encryptor->decrypt($value))],
                    ['config_id = ?' => (int)$configId]
                );
            }
        }
    }

    /**
     * Gather saved credit card numbers from sales order payments and re-encrypt them
     *
     * @return void
     */
    protected function _reEncryptCreditCardNumbers()
    {
        $table = $this->getTable('sales_order_payment');
        $select = $this->getConnection()->select()->from($table, ['entity_id', 'cc_number_enc']);

        $attributeValues = $this->getConnection()->fetchPairs($select);
        // save new values
        foreach ($attributeValues as $valueId => $value) {
            $this->getConnection()->update(
                $table,
                ['cc_number_enc' => $this->encryptor->encrypt($this->encryptor->decrypt($value))],
                ['entity_id = ?' => (int)$valueId]
            );
        }
    }
}
