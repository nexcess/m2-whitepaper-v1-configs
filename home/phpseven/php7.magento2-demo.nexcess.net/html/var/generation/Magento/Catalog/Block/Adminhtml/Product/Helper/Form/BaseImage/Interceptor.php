<?php
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form\BaseImage;

/**
 * Interceptor class for @see
 * \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\BaseImage
 */
class Interceptor extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\BaseImage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\Form\Element\Factory $factoryElement, \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection, \Magento\Framework\Escaper $escaper, \Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Backend\Model\UrlFactory $backendUrlFactory, \Magento\Catalog\Helper\Data $catalogData, \Magento\Framework\File\Size $fileConfig, \Magento\Framework\View\LayoutInterface $layout, array $data = array())
    {
        $this->___init();
        parent::__construct($factoryElement, $factoryCollection, $escaper, $assetRepo, $backendUrlFactory, $catalogData, $fileConfig, $layout, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLabel');
        if (!$pluginInfo) {
            return parent::getLabel();
        } else {
            return $this->___callPlugins('getLabel', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getElementHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getElementHtml');
        if (!$pluginInfo) {
            return parent::getElementHtml();
        } else {
            return $this->___callPlugins('getElementHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assignBlockVariables(\Magento\Framework\View\Element\Template $block)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'assignBlockVariables');
        if (!$pluginInfo) {
            return parent::assignBlockVariables($block);
        } else {
            return $this->___callPlugins('assignBlockVariables', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createElementHtmlOutputBlock()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createElementHtmlOutputBlock');
        if (!$pluginInfo) {
            return parent::createElementHtmlOutputBlock();
        } else {
            return $this->___callPlugins('createElementHtmlOutputBlock', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addElement(\Magento\Framework\Data\Form\Element\AbstractElement $element, $after = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addElement');
        if (!$pluginInfo) {
            return parent::addElement($element, $after);
        } else {
            return $this->___callPlugins('addElement', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAdvanced()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAdvanced');
        if (!$pluginInfo) {
            return parent::isAdvanced();
        } else {
            return $this->___callPlugins('isAdvanced', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setAdvanced($advanced)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setAdvanced');
        if (!$pluginInfo) {
            return parent::setAdvanced($advanced);
        } else {
            return $this->___callPlugins('setAdvanced', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getId');
        if (!$pluginInfo) {
            return parent::getId();
        } else {
            return $this->___callPlugins('getId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getType');
        if (!$pluginInfo) {
            return parent::getType();
        } else {
            return $this->___callPlugins('getType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getForm');
        if (!$pluginInfo) {
            return parent::getForm();
        } else {
            return $this->___callPlugins('getForm', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setId');
        if (!$pluginInfo) {
            return parent::setId($id);
        } else {
            return $this->___callPlugins('setId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHtmlId');
        if (!$pluginInfo) {
            return parent::getHtmlId();
        } else {
            return $this->___callPlugins('getHtmlId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getName');
        if (!$pluginInfo) {
            return parent::getName();
        } else {
            return $this->___callPlugins('getName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setType');
        if (!$pluginInfo) {
            return parent::setType($type);
        } else {
            return $this->___callPlugins('setType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setForm($form)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setForm');
        if (!$pluginInfo) {
            return parent::setForm($form);
        } else {
            return $this->___callPlugins('setForm', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeField($elementId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeField');
        if (!$pluginInfo) {
            return parent::removeField($elementId);
        } else {
            return $this->___callPlugins('removeField', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlAttributes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHtmlAttributes');
        if (!$pluginInfo) {
            return parent::getHtmlAttributes();
        } else {
            return $this->___callPlugins('getHtmlAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addClass($class)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addClass');
        if (!$pluginInfo) {
            return parent::addClass($class);
        } else {
            return $this->___callPlugins('addClass', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeClass($class)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeClass');
        if (!$pluginInfo) {
            return parent::removeClass($class);
        } else {
            return $this->___callPlugins('removeClass', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEscapedValue($index = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEscapedValue');
        if (!$pluginInfo) {
            return parent::getEscapedValue($index);
        } else {
            return $this->___callPlugins('getEscapedValue', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setRenderer(\Magento\Framework\Data\Form\Element\Renderer\RendererInterface $renderer)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setRenderer');
        if (!$pluginInfo) {
            return parent::setRenderer($renderer);
        } else {
            return $this->___callPlugins('setRenderer', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderer()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRenderer');
        if (!$pluginInfo) {
            return parent::getRenderer();
        } else {
            return $this->___callPlugins('getRenderer', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBeforeElementHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBeforeElementHtml');
        if (!$pluginInfo) {
            return parent::getBeforeElementHtml();
        } else {
            return $this->___callPlugins('getBeforeElementHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAfterElementHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAfterElementHtml');
        if (!$pluginInfo) {
            return parent::getAfterElementHtml();
        } else {
            return $this->___callPlugins('getAfterElementHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAfterElementJs()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAfterElementJs');
        if (!$pluginInfo) {
            return parent::getAfterElementJs();
        } else {
            return $this->___callPlugins('getAfterElementJs', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelHtml($idSuffix = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLabelHtml');
        if (!$pluginInfo) {
            return parent::getLabelHtml($idSuffix);
        } else {
            return $this->___callPlugins('getLabelHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDefaultHtml');
        if (!$pluginInfo) {
            return parent::getDefaultHtml();
        } else {
            return $this->___callPlugins('getDefaultHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHtml');
        if (!$pluginInfo) {
            return parent::getHtml();
        } else {
            return $this->___callPlugins('getHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toHtml');
        if (!$pluginInfo) {
            return parent::toHtml();
        } else {
            return $this->___callPlugins('toHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($attributes = array(), $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'serialize');
        if (!$pluginInfo) {
            return parent::serialize($attributes, $valueSeparator, $fieldSeparator, $quote);
        } else {
            return $this->___callPlugins('serialize', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getReadonly()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getReadonly');
        if (!$pluginInfo) {
            return parent::getReadonly();
        } else {
            return $this->___callPlugins('getReadonly', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlContainerId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHtmlContainerId');
        if (!$pluginInfo) {
            return parent::getHtmlContainerId();
        } else {
            return $this->___callPlugins('getHtmlContainerId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addElementValues($values, $overwrite = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addElementValues');
        if (!$pluginInfo) {
            return parent::addElementValues($values, $overwrite);
        } else {
            return $this->___callPlugins('addElementValues', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function lock()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'lock');
        if (!$pluginInfo) {
            return parent::lock();
        } else {
            return $this->___callPlugins('lock', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isLocked');
        if (!$pluginInfo) {
            return parent::isLocked();
        } else {
            return $this->___callPlugins('isLocked', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addType($type, $className)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addType');
        if (!$pluginInfo) {
            return parent::addType($type, $className);
        } else {
            return $this->___callPlugins('addType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getElements()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getElements');
        if (!$pluginInfo) {
            return parent::getElements();
        } else {
            return $this->___callPlugins('getElements', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setReadonly($readonly, $useDisabled = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setReadonly');
        if (!$pluginInfo) {
            return parent::setReadonly($readonly, $useDisabled);
        } else {
            return $this->___callPlugins('setReadonly', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addField($elementId, $type, $config, $after = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addField');
        if (!$pluginInfo) {
            return parent::addField($elementId, $type, $config, $after);
        } else {
            return $this->___callPlugins('addField', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldset($elementId, $config, $after = false, $isAdvanced = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addFieldset');
        if (!$pluginInfo) {
            return parent::addFieldset($elementId, $config, $after, $isAdvanced);
        } else {
            return $this->___callPlugins('addFieldset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn($elementId, $config)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addColumn');
        if (!$pluginInfo) {
            return parent::addColumn($elementId, $config);
        } else {
            return $this->___callPlugins('addColumn', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToArray(array $arrAttributes = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToArray');
        if (!$pluginInfo) {
            return parent::convertToArray($arrAttributes);
        } else {
            return $this->___callPlugins('convertToArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomAttribute($key, $value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addCustomAttribute');
        if (!$pluginInfo) {
            return parent::addCustomAttribute($key, $value);
        } else {
            return $this->___callPlugins('addCustomAttribute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addData(array $arr)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addData');
        if (!$pluginInfo) {
            return parent::addData($arr);
        } else {
            return $this->___callPlugins('addData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setData');
        if (!$pluginInfo) {
            return parent::setData($key, $value);
        } else {
            return $this->___callPlugins('setData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsetData');
        if (!$pluginInfo) {
            return parent::unsetData($key);
        } else {
            return $this->___callPlugins('unsetData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $index = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getData');
        if (!$pluginInfo) {
            return parent::getData($key, $index);
        } else {
            return $this->___callPlugins('getData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByPath($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByPath');
        if (!$pluginInfo) {
            return parent::getDataByPath($path);
        } else {
            return $this->___callPlugins('getDataByPath', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByKey($key)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByKey');
        if (!$pluginInfo) {
            return parent::getDataByKey($key);
        } else {
            return $this->___callPlugins('getDataByKey', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDataUsingMethod($key, $args = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDataUsingMethod');
        if (!$pluginInfo) {
            return parent::setDataUsingMethod($key, $args);
        } else {
            return $this->___callPlugins('setDataUsingMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataUsingMethod($key, $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataUsingMethod');
        if (!$pluginInfo) {
            return parent::getDataUsingMethod($key, $args);
        } else {
            return $this->___callPlugins('getDataUsingMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'hasData');
        if (!$pluginInfo) {
            return parent::hasData($key);
        } else {
            return $this->___callPlugins('hasData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $keys = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toArray');
        if (!$pluginInfo) {
            return parent::toArray($keys);
        } else {
            return $this->___callPlugins('toArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toXml(array $keys = array(), $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toXml');
        if (!$pluginInfo) {
            return parent::toXml($keys, $rootName, $addOpenTag, $addCdata);
        } else {
            return $this->___callPlugins('toXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToXml(array $arrAttributes = array(), $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToXml');
        if (!$pluginInfo) {
            return parent::convertToXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
        } else {
            return $this->___callPlugins('convertToXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toJson(array $keys = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toJson');
        if (!$pluginInfo) {
            return parent::toJson($keys);
        } else {
            return $this->___callPlugins('toJson', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToJson(array $keys = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToJson');
        if (!$pluginInfo) {
            return parent::convertToJson($keys);
        } else {
            return $this->___callPlugins('convertToJson', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toString($format = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toString');
        if (!$pluginInfo) {
            return parent::toString($format);
        } else {
            return $this->___callPlugins('toString', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $args)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__call');
        if (!$pluginInfo) {
            return parent::__call($method, $args);
        } else {
            return $this->___callPlugins('__call', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isEmpty');
        if (!$pluginInfo) {
            return parent::isEmpty();
        } else {
            return $this->___callPlugins('isEmpty', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function debug($data = null, &$objects = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'debug');
        if (!$pluginInfo) {
            return parent::debug($data, $objects);
        } else {
            return $this->___callPlugins('debug', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetSet');
        if (!$pluginInfo) {
            return parent::offsetSet($offset, $value);
        } else {
            return $this->___callPlugins('offsetSet', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetExists');
        if (!$pluginInfo) {
            return parent::offsetExists($offset);
        } else {
            return $this->___callPlugins('offsetExists', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetUnset');
        if (!$pluginInfo) {
            return parent::offsetUnset($offset);
        } else {
            return $this->___callPlugins('offsetUnset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetGet');
        if (!$pluginInfo) {
            return parent::offsetGet($offset);
        } else {
            return $this->___callPlugins('offsetGet', func_get_args(), $pluginInfo);
        }
    }
}
