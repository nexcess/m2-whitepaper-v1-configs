<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Test\Unit\Controller;

use \Magento\Setup\Controller\Install;

class InstallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Setup\Model\WebLogger
     */
    private $webLogger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Setup\Model\Installer
     */
    private $installer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Setup\Model\Installer\ProgressFactory
     */
    private $progressFactory;

    /**
     * @var Install
     */
    private $controller;

    /**
     * @var \Magento\Framework\Setup\SampleData\State|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sampleDataState;

    public function setUp()
    {
        $this->webLogger = $this->getMock('\Magento\Setup\Model\WebLogger', [], [], '', false);
        $installerFactory = $this->getMock('\Magento\Setup\Model\InstallerFactory', [], [], '', false);
        $this->installer = $this->getMock('\Magento\Setup\Model\Installer', [], [], '', false);
        $this->progressFactory = $this->getMock('\Magento\Setup\Model\Installer\ProgressFactory', [], [], '', false);
        $this->sampleDataState = $this->getMock('\Magento\Framework\Setup\SampleData\State', [], [], '', false);

        $installerFactory->expects($this->once())->method('create')->with($this->webLogger)
            ->willReturn($this->installer);
        $this->controller = new Install(
            $this->webLogger,
            $installerFactory,
            $this->progressFactory,
            $this->sampleDataState
        );
    }

    public function testIndexAction()
    {
        $viewModel = $this->controller->indexAction();
        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $viewModel);
        $this->assertTrue($viewModel->terminate());
    }

    public function testStartAction()
    {
        $this->webLogger->expects($this->once())->method('clear');
        $this->installer->expects($this->once())->method('install');
        $this->installer->expects($this->exactly(2))->method('getInstallInfo');
        $jsonModel = $this->controller->startAction();
        $this->assertInstanceOf('\Zend\View\Model\JsonModel', $jsonModel);
        $variables = $jsonModel->getVariables();
        $this->assertArrayHasKey('key', $variables);
        $this->assertArrayHasKey('success', $variables);
        $this->assertArrayHasKey('messages', $variables);
        $this->assertTrue($variables['success']);
    }

    public function testStartActionException()
    {
        $this->webLogger->expects($this->once())->method('clear');
        $this->installer->expects($this->once())->method('install')
            ->willThrowException($this->getMock('\Exception'));
        $jsonModel = $this->controller->startAction();
        $this->assertNull($jsonModel->getVariable('isSampleDataError'));
    }

    public function testStartActionWithSampleDataError()
    {
        $this->webLogger->expects($this->once())->method('clear');
        $this->webLogger->expects($this->never())->method('logError');
        $this->installer->method('install');
        $this->sampleDataState->expects($this->once())->method('hasError')->willReturn(true);
        $jsonModel = $this->controller->startAction();
        $this->assertInstanceOf('\Zend\View\Model\JsonModel', $jsonModel);
        $variables = $jsonModel->getVariables();
        $this->assertArrayHasKey('success', $variables);
        $this->assertTrue($variables['success']);
        $this->assertTrue($jsonModel->getVariable('isSampleDataError'));
    }

    public function testProgressAction()
    {
        $numValue = 42;
        $consoleMessages = ['key1' => 'log message 1', 'key2' => 'log message 2'];

        $this->webLogger->expects($this->once())->method('logfileExists')->willReturn(true);
        $progress = $this->getMock('\Magento\Setup\Model\Installer\Progress', [], [], '', false);
        $this->progressFactory->expects($this->once())->method('createFromLog')->with($this->webLogger)
            ->willReturn($progress);
        $progress->expects($this->once())->method('getRatio')->willReturn($numValue);
        $this->webLogger->expects($this->once())->method('get')->willReturn($consoleMessages);
        $jsonModel = $this->controller->progressAction();
        $this->assertInstanceOf('\Zend\View\Model\JsonModel', $jsonModel);
        $variables = $jsonModel->getVariables();
        $this->assertArrayHasKey('progress', $variables);
        $this->assertArrayHasKey('success', $variables);
        $this->assertArrayHasKey('console', $variables);
        $this->assertSame($consoleMessages, $variables['console']);
        $this->assertTrue($variables['success']);
        $this->assertSame(sprintf('%d', $numValue * 100), $variables['progress']);
    }

    public function testProgressActionWithError()
    {
        $e = 'Some exception message';
        $this->webLogger->expects($this->once())->method('logfileExists')->willReturn(true);
        $this->progressFactory->expects($this->once())->method('createFromLog')
            ->will($this->throwException(new \LogicException($e)));
        $jsonModel = $this->controller->progressAction();
        $this->assertInstanceOf('\Zend\View\Model\JsonModel', $jsonModel);
        $variables = $jsonModel->getVariables();
        $this->assertArrayHasKey('success', $variables);
        $this->assertArrayHasKey('console', $variables);
        $this->assertFalse($variables['success']);
        $this->assertContains('LogicException', $variables['console'][0]);
        $this->assertContains($e, $variables['console'][0]);
    }

    public function testProgressActionWithSampleDataError()
    {
        $numValue = 42;
        $this->webLogger->expects($this->once())->method('logfileExists')->willReturn(true);
        $progress = $this->getMock('\Magento\Setup\Model\Installer\Progress', [], [], '', false);
        $progress->expects($this->once())->method('getRatio')->willReturn($numValue);
        $this->progressFactory->expects($this->once())->method('createFromLog')->willReturn($progress);
        $this->sampleDataState->expects($this->once())->method('hasError')->willReturn(true);
        $jsonModel = $this->controller->progressAction();
        $this->assertInstanceOf('\Zend\View\Model\JsonModel', $jsonModel);
        $variables = $jsonModel->getVariables();
        $this->assertArrayHasKey('success', $variables);
        $this->assertArrayHasKey('console', $variables);
        $this->assertTrue($variables['success']);
        $this->assertTrue($jsonModel->getVariable('isSampleDataError'));
        $this->assertSame(sprintf('%d', $numValue * 100), $variables['progress']);
    }

    public function testProgressActionNoInstallLogFile()
    {
        $this->webLogger->expects($this->once())->method('logfileExists')->willReturn(false);
        $jsonModel = $this->controller->progressAction();
        $this->assertInstanceOf('\Zend\View\Model\JsonModel', $jsonModel);
        $variables = $jsonModel->getVariables();
        $this->assertArrayHasKey('success', $variables);
        $this->assertArrayHasKey('console', $variables);
        $this->assertTrue($variables['success']);
        $this->assertEmpty($variables['console']);
        $this->assertSame(0, $variables['progress']);
    }

    public function testDispatch()
    {
        $request = $this->getMock('\Zend\Http\PhpEnvironment\Request', [], [], '', false);
        $response = $this->getMock('\Zend\Http\PhpEnvironment\Response', [], [], '', false);
        $routeMatch = $this->getMock('\Zend\Mvc\Router\RouteMatch', [], [], '', false);

        $mvcEvent = $this->getMock('\Zend\Mvc\MvcEvent', [], [], '', false);
        $mvcEvent->expects($this->once())->method('setRequest')->with($request)->willReturn($mvcEvent);
        $mvcEvent->expects($this->once())->method('setResponse')->with($response)->willReturn($mvcEvent);
        $mvcEvent->expects($this->once())->method('setTarget')->with($this->controller)->willReturn($mvcEvent);
        $mvcEvent->expects($this->any())->method('getRouteMatch')->willReturn($routeMatch);

        $contentArray = '{"config": { "address": { "base_url": "http://123.45.678.12"}}}';
        $request->expects($this->any())->method('getContent')->willReturn($contentArray);
        $this->controller->setEvent($mvcEvent);
        $this->controller->dispatch($request, $response);
        $this->controller->startAction();
    }
}
