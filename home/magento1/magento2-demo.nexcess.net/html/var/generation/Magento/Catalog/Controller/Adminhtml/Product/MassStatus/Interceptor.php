<?php
namespace Magento\Catalog\Controller\Adminhtml\Product\MassStatus;

/**
 * Interceptor class for @see
 * \Magento\Catalog\Controller\Adminhtml\Product\MassStatus
 */
class Interceptor extends \Magento\Catalog\Controller\Adminhtml\Product\MassStatus implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder, \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor, \Magento\Ui\Component\MassAction\Filter $filter, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $productBuilder, $productPriceIndexerProcessor, $filter, $collectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function _validateMassStatus(array $productIds, $status)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '_validateMassStatus');
        if (!$pluginInfo) {
            return parent::_validateMassStatus($productIds, $status);
        } else {
            return $this->___callPlugins('_validateMassStatus', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute();
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function _processUrlKeys()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '_processUrlKeys');
        if (!$pluginInfo) {
            return parent::_processUrlKeys();
        } else {
            return $this->___callPlugins('_processUrlKeys', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($route = '', $params = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUrl');
        if (!$pluginInfo) {
            return parent::getUrl($route, $params);
        } else {
            return $this->___callPlugins('getUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        if (!$pluginInfo) {
            return parent::getActionFlag();
        } else {
            return $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        if (!$pluginInfo) {
            return parent::getRequest();
        } else {
            return $this->___callPlugins('getRequest', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        if (!$pluginInfo) {
            return parent::getResponse();
        } else {
            return $this->___callPlugins('getResponse', func_get_args(), $pluginInfo);
        }
    }
}
