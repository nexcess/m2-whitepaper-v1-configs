<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Test\Unit\Controller;

use \Magento\Setup\Controller\CompleteBackup;

class CompleteBackupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Controller
     *
     * @var \Magento\Setup\Controller\CompleteBackup
     */
    private $controller;

    public function setUp()
    {
        $this->controller = new CompleteBackup();
    }

    public function testIndexAction()
    {
        $viewModel = $this->controller->indexAction();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $viewModel);
        $this->assertTrue($viewModel->terminate());
        $this->assertSame('/magento/setup/complete-backup.phtml', $viewModel->getTemplate());
    }

    public function testProgressAction()
    {
        $viewModel = $this->controller->progressAction();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $viewModel);
        $this->assertTrue($viewModel->terminate());
        $this->assertSame('/magento/setup/complete-backup/progress.phtml', $viewModel->getTemplate());
    }
}
