<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backup\Controller\Adminhtml;

/**
 * Backup admin controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Index extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Backup\Factory
     */
    protected $_backupFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Backup\Model\BackupFactory
     */
    protected $_backupModelFactory;

    /**
     * @var \Magento\Framework\App\MaintenanceMode
     */
    protected $maintenanceMode;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Backup\Factory $backupFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Backup\Model\BackupFactory $backupModelFactory
     * @param \Magento\Framework\App\MaintenanceMode $maintenanceMode
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Backup\Factory $backupFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backup\Model\BackupFactory $backupModelFactory,
        \Magento\Framework\App\MaintenanceMode $maintenanceMode
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_backupFactory = $backupFactory;
        $this->_fileFactory = $fileFactory;
        $this->_backupModelFactory = $backupModelFactory;
        $this->maintenanceMode = $maintenanceMode;
        parent::__construct($context);
    }

    /**
     * Check Permissions for all actions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Backup::backup');
    }
}
