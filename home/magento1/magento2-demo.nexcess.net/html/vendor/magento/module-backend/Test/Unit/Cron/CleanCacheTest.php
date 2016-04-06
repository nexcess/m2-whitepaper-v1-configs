<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Test\Unit\Cron;

class CleanCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testCleanCache()
    {
        $cacheBackendMock = $this->getMockForAbstractClass('Zend_Cache_Backend_Interface');
        $cacheFrontendMock = $this->getMockForAbstractClass('Magento\Framework\Cache\FrontendInterface');
        $frontendPoolMock = $this->getMock(
            'Magento\Framework\App\Cache\Frontend\Pool',
            [],
            [],
            '',
            false
        );

        $cacheBackendMock->expects(
            $this->once()
        )->method(
            'clean'
        )->with(
            \Zend_Cache::CLEANING_MODE_OLD,
            []
        );

        $cacheFrontendMock->expects(
            $this->once()
        )->method(
            'getBackend'
        )->will(
            $this->returnValue($cacheBackendMock)
        );

        $frontendPoolMock->expects(
            $this->any()
        )->method(
            'valid'
        )->will(
            $this->onConsecutiveCalls(true, false)
        );

        $frontendPoolMock->expects(
            $this->any()
        )->method(
            'current'
        )->will(
            $this->returnValue($cacheFrontendMock)
        );

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /**
         * @var \Magento\Backend\Cron\CleanCache
         */
        $model = $objectManagerHelper->getObject(
            'Magento\Backend\Cron\CleanCache',
            [
                'cacheFrontendPool' => $frontendPoolMock,
            ]
        );

        $model->execute();
    }
}
