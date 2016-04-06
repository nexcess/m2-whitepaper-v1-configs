<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Config\Model\Config\Backend\Admin;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @magentoAppArea adminhtml
 */
class RobotsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Config\Model\Config\Backend\Admin\Robots
     */
    protected $model = null;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $rootDirectory;

    /**
     * Initialize model
     */
    protected function setUp()
    {
        parent::setUp();

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $objectManager->create('Magento\Config\Model\Config\Backend\Admin\Robots');
        $this->model->setPath('design/search_engine_robots/custom_instructions');
        $this->model->afterLoad();
        $this->rootDirectory = $objectManager->get(
            'Magento\Framework\Filesystem'
        )->getDirectoryRead(
            DirectoryList::ROOT
        );
    }

    /**
     * Check that default value is empty when robots.txt not exists
     *
     * @magentoDataFixture Magento/Config/Model/_files/no_robots_txt.php
     */
    public function testAfterLoadRobotsTxtNotExists()
    {
        $this->assertEmpty($this->model->getValue());
    }

    /**
     * Check that default value equals to robots.txt content when it is available
     *
     * @magentoDataFixture Magento/Config/Model/_files/robots_txt.php
     */
    public function testAfterLoadRobotsTxtExists()
    {
        $this->assertEquals('Sitemap: http://store.com/sitemap.xml', $this->model->getValue());
    }

    /**
     * Check robots.txt file generated when robots.txt not exists
     *
     * @magentoDbIsolation enabled
     */
    public function testAfterSaveFileNotExists()
    {
        $this->assertFalse($this->rootDirectory->isExist('robots.txt'), 'robots.txt exists');

        $this->_modifyConfig();
    }

    /**
     * Check robots.txt file changed when robots.txt exists
     *
     * @magentoDataFixture Magento/Config/Model/_files/robots_txt.php
     * @magentoDbIsolation enabled
     */
    public function testAfterSaveFileExists()
    {
        $this->assertTrue($this->rootDirectory->isExist('robots.txt'), 'robots.txt not exists');

        $this->_modifyConfig();
    }

    /**
     * Modify config value and check all changes were written into robots.txt
     */
    protected function _modifyConfig()
    {
        $robotsTxt = "User-Agent: *\nDisallow: /checkout";
        $this->model->setValue($robotsTxt)->save();
        $this->assertStringEqualsFile($this->rootDirectory->getAbsolutePath('robots.txt'), $robotsTxt);
    }

    /**
     * Remove created robots.txt
     */
    protected function tearDown()
    {
        require 'Magento/Config/Model/_files/no_robots_txt.php';
    }
}
