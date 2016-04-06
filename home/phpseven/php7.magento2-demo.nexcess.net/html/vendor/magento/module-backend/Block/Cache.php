<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Block;

class Cache extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'cache';
        $this->_headerText = __('Cache Storage Management');
        parent::_construct();
        $this->buttonList->remove('add');
        $this->buttonList->add(
            'flush_magento',
            [
                'label' => __('Flush Magento Cache'),
                'onclick' => 'setLocation(\'' . $this->getFlushSystemUrl() . '\')',
                'class' => 'primary flush-cache-magento'
            ]
        );

        $message = __('The cache storage may contain additional data. Are you sure that you want to flush it?');
        $this->buttonList->add(
            'flush_system',
            [
                'label' => __('Flush Cache Storage'),
                'onclick' => 'confirmSetLocation(\'' . $message . '\', \'' . $this->getFlushStorageUrl() . '\')',
                'class' => 'flush-cache-storage'
            ]
        );
    }

    /**
     * Get url for clean cache storage
     *
     * @return string
     */
    public function getFlushStorageUrl()
    {
        return $this->getUrl('adminhtml/*/flushAll');
    }

    /**
     * Get url for clean cache storage
     *
     * @return string
     */
    public function getFlushSystemUrl()
    {
        return $this->getUrl('adminhtml/*/flushSystem');
    }
}
