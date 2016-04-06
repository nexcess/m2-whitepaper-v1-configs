<?php
namespace Magento\Webapi\Controller\Soap;

/**
 * Interceptor class for @see \Magento\Webapi\Controller\Soap
 */
class Interceptor extends \Magento\Webapi\Controller\Soap implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Webapi\Request $request, \Magento\Framework\Webapi\Response $response, \Magento\Webapi\Model\Soap\Wsdl\Generator $wsdlGenerator, \Magento\Webapi\Model\Soap\Server $soapServer, \Magento\Framework\Webapi\ErrorProcessor $errorProcessor, \Magento\Framework\App\State $appState, \Magento\Framework\Locale\ResolverInterface $localeResolver, \Magento\Webapi\Controller\PathProcessor $pathProcessor, \Magento\Framework\Webapi\Rest\Response\RendererFactory $rendererFactory, \Magento\Framework\App\AreaList $areaList)
    {
        $this->___init();
        parent::__construct($request, $response, $wsdlGenerator, $soapServer, $errorProcessor, $appState, $localeResolver, $pathProcessor, $rendererFactory, $areaList);
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
}
