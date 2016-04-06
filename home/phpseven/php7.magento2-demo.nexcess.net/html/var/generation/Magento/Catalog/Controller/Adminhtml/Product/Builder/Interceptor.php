<?php
namespace Magento\Catalog\Controller\Adminhtml\Product\Builder;

/**
 * Interceptor class for @see \Magento\Catalog\Controller\Adminhtml\Product\Builder
 */
class Interceptor extends \Magento\Catalog\Controller\Adminhtml\Product\Builder implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\ProductFactory $productFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Registry $registry, \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig)
    {
        $this->___init();
        parent::__construct($productFactory, $logger, $registry, $wysiwygConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function build(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'build');
        if (!$pluginInfo) {
            return parent::build($request);
        } else {
            return $this->___callPlugins('build', func_get_args(), $pluginInfo);
        }
    }
}
