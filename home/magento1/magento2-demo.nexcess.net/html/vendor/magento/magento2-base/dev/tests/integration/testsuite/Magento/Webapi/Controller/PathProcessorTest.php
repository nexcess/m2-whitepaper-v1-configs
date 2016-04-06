<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi\Controller;

use Magento\Store\Model\Store;

class PathProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Webapi\Controller\PathProcessor
     */
    protected $pathProcessor;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->storeManager->reinitStores();
        $this->pathProcessor = $objectManager->get('Magento\Webapi\Controller\PathProcessor');
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     */
    public function testProcessWithValidStoreCode()
    {
        $storeCode = 'fixturestore';
        $basePath = "rest/{$storeCode}";
        $path = $basePath . '/V1/customerAccounts/createCustomer';
        $resultPath = $this->pathProcessor->process($path);
        $this->assertEquals(str_replace($basePath, "", $path), $resultPath);
        $this->assertEquals($storeCode, $this->storeManager->getStore()->getCode());
    }

    public function testProcessWithAllStoreCode()
    {
        $storeCode = 'all';
        $path = '/V1/customerAccounts/createCustomer';
        $uri = 'rest/' . $storeCode . $path;
        $result = $this->pathProcessor->process($uri);
        $this->assertEquals($path, $result);
        $this->assertEquals(Store::ADMIN_CODE, $this->storeManager->getStore()->getCode());
    }


    public function testProcessWithoutStoreCode()
    {
        $path = '/V1/customerAccounts/createCustomer';
        $uri = 'rest' . $path;
        $result = $this->pathProcessor->process($uri);
        $this->assertEquals($path, $result);
        $this->assertEquals('default', $this->storeManager->getStore()->getCode());
    }
}
