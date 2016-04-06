<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Helper\Dashboard;

/**
 * Adminhtml abstract  dashboard helper.
 */
abstract class AbstractDashboard extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Helper collection
     *
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|array
     */
    protected $_collection;

    /**
     * Parameters for helper
     *
     * @var array
     */
    protected $_params = [];

    /**
     * @return array|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        if ($this->_collection === null) {
            $this->_initCollection();
        }
        return $this->_collection;
    }

    /**
     * @return void
     */
    abstract protected function _initCollection();

    /**
     * Returns collection items
     *
     * @return array
     */
    public function getItems()
    {
        return is_array($this->getCollection()) ? $this->getCollection() : $this->getCollection()->getItems();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return sizeof($this->getItems());
    }

    /**
     * @param string $index
     * @return array
     */
    public function getColumn($index)
    {
        $result = [];
        foreach ($this->getItems() as $item) {
            if (is_array($item)) {
                if (isset($item[$index])) {
                    $result[] = $item[$index];
                } else {
                    $result[] = null;
                }
            } elseif ($item instanceof \Magento\Framework\DataObject) {
                $result[] = $item->getData($index);
            } else {
                $result[] = null;
            }
        }
        return $result;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
    }

    /**
     * @param array $params
     * @return void
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getParam($name)
    {
        if (isset($this->_params[$name])) {
            return $this->_params[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
}
