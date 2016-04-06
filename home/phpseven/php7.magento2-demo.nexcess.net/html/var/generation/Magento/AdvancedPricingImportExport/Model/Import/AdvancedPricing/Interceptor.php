<?php
namespace Magento\AdvancedPricingImportExport\Model\Import\AdvancedPricing;

/**
 * Interceptor class for @see
 * \Magento\AdvancedPricingImportExport\Model\Import\AdvancedPricing
 */
class Interceptor extends \Magento\AdvancedPricingImportExport\Model\Import\AdvancedPricing implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\ImportExport\Helper\Data $importExportData, \Magento\ImportExport\Model\ResourceModel\Import\Data $importData, \Magento\Eav\Model\Config $config, \Magento\Framework\App\ResourceConnection $resource, \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper, \Magento\Framework\Stdlib\StringUtils $string, \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator, \Magento\Framework\Stdlib\DateTime\DateTime $dateTime, \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory, \Magento\Catalog\Model\Product $productModel, \Magento\Catalog\Helper\Data $catalogData, \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver, \Magento\CatalogImportExport\Model\Import\Product $importProduct, \Magento\AdvancedPricingImportExport\Model\Import\AdvancedPricing\Validator $validator, \Magento\AdvancedPricingImportExport\Model\Import\AdvancedPricing\Validator\Website $websiteValidator, \Magento\AdvancedPricingImportExport\Model\Import\AdvancedPricing\Validator\TierPrice $tierPriceValidator)
    {
        $this->___init();
        parent::__construct($jsonHelper, $importExportData, $importData, $config, $resource, $resourceHelper, $string, $errorAggregator, $dateTime, $resourceFactory, $productModel, $catalogData, $storeResolver, $importProduct, $validator, $websiteValidator, $tierPriceValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTypeCode()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntityTypeCode');
        if (!$pluginInfo) {
            return parent::getEntityTypeCode();
        } else {
            return $this->___callPlugins('getEntityTypeCode', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validateRow');
        if (!$pluginInfo) {
            return parent::validateRow($rowData, $rowNum);
        } else {
            return $this->___callPlugins('validateRow', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveAdvancedPricing()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'saveAdvancedPricing');
        if (!$pluginInfo) {
            return parent::saveAdvancedPricing();
        } else {
            return $this->___callPlugins('saveAdvancedPricing', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAdvancedPricing()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'deleteAdvancedPricing');
        if (!$pluginInfo) {
            return parent::deleteAdvancedPricing();
        } else {
            return $this->___callPlugins('deleteAdvancedPricing', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replaceAdvancedPricing()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'replaceAdvancedPricing');
        if (!$pluginInfo) {
            return parent::replaceAdvancedPricing();
        } else {
            return $this->___callPlugins('replaceAdvancedPricing', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addRowError($errorCode, $errorRowNum, $colName = null, $errorMessage = null, $errorLevel = 'critical', $errorDescription = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addRowError');
        if (!$pluginInfo) {
            return parent::addRowError($errorCode, $errorRowNum, $colName, $errorMessage, $errorLevel, $errorDescription);
        } else {
            return $this->___callPlugins('addRowError', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addMessageTemplate($errorCode, $message)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addMessageTemplate');
        if (!$pluginInfo) {
            return parent::addMessageTemplate($errorCode, $message);
        } else {
            return $this->___callPlugins('addMessageTemplate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeOptions(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute, $indexValAttrs = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributeOptions');
        if (!$pluginInfo) {
            return parent::getAttributeOptions($attribute, $indexValAttrs);
        } else {
            return $this->___callPlugins('getAttributeOptions', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBehavior()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBehavior');
        if (!$pluginInfo) {
            return parent::getBehavior();
        } else {
            return $this->___callPlugins('getBehavior', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTypeId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntityTypeId');
        if (!$pluginInfo) {
            return parent::getEntityTypeId();
        } else {
            return $this->___callPlugins('getEntityTypeId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedEntitiesCount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProcessedEntitiesCount');
        if (!$pluginInfo) {
            return parent::getProcessedEntitiesCount();
        } else {
            return $this->___callPlugins('getProcessedEntitiesCount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedRowsCount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getProcessedRowsCount');
        if (!$pluginInfo) {
            return parent::getProcessedRowsCount();
        } else {
            return $this->___callPlugins('getProcessedRowsCount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSource');
        if (!$pluginInfo) {
            return parent::getSource();
        } else {
            return $this->___callPlugins('getSource', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function importData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'importData');
        if (!$pluginInfo) {
            return parent::importData();
        } else {
            return $this->___callPlugins('importData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAttributeParticular($attrCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAttributeParticular');
        if (!$pluginInfo) {
            return parent::isAttributeParticular($attrCode);
        } else {
            return $this->___callPlugins('isAttributeParticular', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAttributeValid($attrCode, array $attrParams, array $rowData, $rowNum)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAttributeValid');
        if (!$pluginInfo) {
            return parent::isAttributeValid($attrCode, $attrParams, $rowData, $rowNum);
        } else {
            return $this->___callPlugins('isAttributeValid', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isImportAllowed()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isImportAllowed');
        if (!$pluginInfo) {
            return parent::isImportAllowed();
        } else {
            return $this->___callPlugins('isImportAllowed', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isRowAllowedToImport(array $rowData, $rowNum)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isRowAllowedToImport');
        if (!$pluginInfo) {
            return parent::isRowAllowedToImport($rowData, $rowNum);
        } else {
            return $this->___callPlugins('isRowAllowedToImport', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveMessageTemplate($errorCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'retrieveMessageTemplate');
        if (!$pluginInfo) {
            return parent::retrieveMessageTemplate($errorCode);
        } else {
            return $this->___callPlugins('retrieveMessageTemplate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isNeedToLogInHistory()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isNeedToLogInHistory');
        if (!$pluginInfo) {
            return parent::isNeedToLogInHistory();
        } else {
            return $this->___callPlugins('isNeedToLogInHistory', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $params)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setParameters');
        if (!$pluginInfo) {
            return parent::setParameters($params);
        } else {
            return $this->___callPlugins('setParameters', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getParameters');
        if (!$pluginInfo) {
            return parent::getParameters();
        } else {
            return $this->___callPlugins('getParameters', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setSource(\Magento\ImportExport\Model\Import\AbstractSource $source)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setSource');
        if (!$pluginInfo) {
            return parent::setSource($source);
        } else {
            return $this->___callPlugins('setSource', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validateData');
        if (!$pluginInfo) {
            return parent::validateData();
        } else {
            return $this->___callPlugins('validateData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorAggregator()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getErrorAggregator');
        if (!$pluginInfo) {
            return parent::getErrorAggregator();
        } else {
            return $this->___callPlugins('getErrorAggregator', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedItemsCount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCreatedItemsCount');
        if (!$pluginInfo) {
            return parent::getCreatedItemsCount();
        } else {
            return $this->___callPlugins('getCreatedItemsCount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedItemsCount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUpdatedItemsCount');
        if (!$pluginInfo) {
            return parent::getUpdatedItemsCount();
        } else {
            return $this->___callPlugins('getUpdatedItemsCount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedItemsCount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDeletedItemsCount');
        if (!$pluginInfo) {
            return parent::getDeletedItemsCount();
        } else {
            return $this->___callPlugins('getDeletedItemsCount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValidColumnNames()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getValidColumnNames');
        if (!$pluginInfo) {
            return parent::getValidColumnNames();
        } else {
            return $this->___callPlugins('getValidColumnNames', func_get_args(), $pluginInfo);
        }
    }
}
