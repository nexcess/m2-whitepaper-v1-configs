<?php
namespace Magento\Integration\Model\ConfigBasedIntegrationManager;

/**
 * Interceptor class for @see
 * \Magento\Integration\Model\ConfigBasedIntegrationManager
 */
class Interceptor extends \Magento\Integration\Model\ConfigBasedIntegrationManager implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Integration\Model\Config $integrationConfig, \Magento\Integration\Api\IntegrationServiceInterface $integrationService)
    {
        $this->___init();
        parent::__construct($integrationConfig, $integrationService);
    }

    /**
     * {@inheritdoc}
     */
    public function processIntegrationConfig(array $integrationNames)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'processIntegrationConfig');
        if (!$pluginInfo) {
            return parent::processIntegrationConfig($integrationNames);
        } else {
            return $this->___callPlugins('processIntegrationConfig', func_get_args(), $pluginInfo);
        }
    }
}
