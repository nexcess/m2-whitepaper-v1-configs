<?php
namespace Magento\Indexer\Model\Processor;

/**
 * Interceptor class for @see \Magento\Indexer\Model\Processor
 */
class Interceptor extends \Magento\Indexer\Model\Processor implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Indexer\ConfigInterface $config, \Magento\Indexer\Model\IndexerFactory $indexerFactory, \Magento\Indexer\Model\Indexer\CollectionFactory $indexersFactory, \Magento\Framework\Mview\ProcessorInterface $mviewProcessor)
    {
        $this->___init();
        parent::__construct($config, $indexerFactory, $indexersFactory, $mviewProcessor);
    }

    /**
     * {@inheritdoc}
     */
    public function reindexAllInvalid()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'reindexAllInvalid');
        if (!$pluginInfo) {
            return parent::reindexAllInvalid();
        } else {
            return $this->___callPlugins('reindexAllInvalid', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reindexAll()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'reindexAll');
        if (!$pluginInfo) {
            return parent::reindexAll();
        } else {
            return $this->___callPlugins('reindexAll', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateMview()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'updateMview');
        if (!$pluginInfo) {
            return parent::updateMview();
        } else {
            return $this->___callPlugins('updateMview', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearChangelog()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'clearChangelog');
        if (!$pluginInfo) {
            return parent::clearChangelog();
        } else {
            return $this->___callPlugins('clearChangelog', func_get_args(), $pluginInfo);
        }
    }
}
