<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Test\Unit;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\SetupInfo;
use Magento\Framework\App\Bootstrap;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\App\Http
     */
    protected $http;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $frontControllerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $areaListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $cookieReaderMock = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\CookieReaderInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $routeConfigMock = $this->getMockBuilder('Magento\Framework\App\Route\ConfigInterface\Proxy')
            ->disableOriginalConstructor()
            ->getMock();
        $pathInfoProcessorMock = $this->getMockBuilder('Magento\Framework\App\Request\PathInfoProcessorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $converterMock = $this->getMockBuilder('Magento\Framework\Stdlib\StringUtils')
            ->disableOriginalConstructor()
            ->setMethods(['cleanString'])
            ->getMock();
        $objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->setConstructorArgs([
                'cookieReader' => $cookieReaderMock,
                'converter' => $converterMock,
                'routeConfig' => $routeConfigMock,
                'pathInfoProcessor' => $pathInfoProcessorMock,
                'objectManager' => $objectManagerMock
            ])
            ->setMethods(['getFrontName'])
            ->getMock();
        $this->areaListMock = $this->getMockBuilder('Magento\Framework\App\AreaList')
            ->disableOriginalConstructor()
            ->setMethods(['getCodeByFrontName'])
            ->getMock();
        $this->configLoaderMock = $this->getMockBuilder('Magento\Framework\App\ObjectManager\ConfigLoader')
            ->disableOriginalConstructor()
            ->setMethods(['load'])
            ->getMock();
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->responseMock = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);
        $this->frontControllerMock = $this->getMockBuilder('Magento\Framework\App\FrontControllerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock();
        $this->eventManagerMock = $this->getMockBuilder('Magento\Framework\Event\Manager')
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock();
        $this->filesystemMock = $this->getMock('Magento\Framework\Filesystem', [], [], '', false);

        $this->http = $this->objectManager->getObject(
            'Magento\Framework\App\Http',
            [
                'objectManager' => $this->objectManagerMock,
                'eventManager' => $this->eventManagerMock,
                'areaList' => $this->areaListMock,
                'request' => $this->requestMock,
                'response' => $this->responseMock,
                'configLoader' => $this->configLoaderMock,
                'filesystem' => $this->filesystemMock,
            ]
        );
    }

    /**
     * Asserts mock objects with methods that are expected to be called when http->launch() is invoked.
     */
    private function setUpLaunch()
    {
        $frontName = 'frontName';
        $areaCode = 'areaCode';
        $this->requestMock->expects($this->once())->method('getFrontName')->will($this->returnValue($frontName));
        $this->areaListMock->expects($this->once())
            ->method('getCodeByFrontName')
            ->with($frontName)->will($this->returnValue($areaCode));
        $this->configLoaderMock->expects($this->once())
            ->method('load')->with($areaCode)->will($this->returnValue([]));
        $this->objectManagerMock->expects($this->once())->method('configure')->with([]);
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\App\FrontControllerInterface')
            ->will($this->returnValue($this->frontControllerMock));
        $this->frontControllerMock->expects($this->once())
            ->method('dispatch')
            ->with($this->requestMock)
            ->will($this->returnValue($this->responseMock));
    }

    public function testLaunchSuccess()
    {
        $this->setUpLaunch();
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with(
                'controller_front_send_response_before',
                ['request' => $this->requestMock, 'response' => $this->responseMock]
            );
        $this->assertSame($this->responseMock, $this->http->launch());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Message
     */
    public function testLaunchException()
    {
        $this->setUpLaunch();
        $this->frontControllerMock->expects($this->once())->method('dispatch')->with($this->requestMock)->will(
            $this->returnCallback(
                function () {
                    throw new \Exception('Message');
                }
            )
        );
        $this->http->launch();
    }

    public function testHandleDeveloperModeNotInstalled()
    {
        $dir = $this->getMockForAbstractClass('Magento\Framework\Filesystem\Directory\ReadInterface');
        $dir->expects($this->once())->method('getAbsolutePath')->willReturn(__DIR__);
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::ROOT)
            ->willReturn($dir);
        $this->responseMock->expects($this->once())->method('setRedirect')->with('/_files/');
        $this->responseMock->expects($this->once())->method('sendHeaders');
        $bootstrap = $this->getBootstrapNotInstalled();
        $bootstrap->expects($this->once())->method('getParams')->willReturn([
            'SCRIPT_NAME' => '/index.php',
            'DOCUMENT_ROOT' => __DIR__,
            'SCRIPT_FILENAME' => __DIR__ . '/index.php',
            SetupInfo::PARAM_NOT_INSTALLED_URL_PATH => '_files',
        ]);
        $this->assertTrue($this->http->catchException($bootstrap, new \Exception('Test Message')));
    }

    public function testHandleDeveloperMode()
    {
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->will($this->throwException(new \Exception('strange error')));
        $this->responseMock->expects($this->once())->method('setHttpResponseCode')->with(500);
        $this->responseMock->expects($this->once())->method('setHeader')->with('Content-Type', 'text/plain');
        $constraint = new \PHPUnit_Framework_Constraint_StringStartsWith('strange error');
        $this->responseMock->expects($this->once())->method('setBody')->with($constraint);
        $this->responseMock->expects($this->once())->method('sendResponse');
        $bootstrap = $this->getBootstrapNotInstalled();
        $bootstrap->expects($this->once())->method('getParams')->willReturn(
            ['DOCUMENT_ROOT' => 'something', 'SCRIPT_FILENAME' => 'something/else']
        );
        $this->assertTrue($this->http->catchException($bootstrap, new \Exception('Test')));
    }

    public function testCatchExceptionSessionException()
    {
        $this->responseMock->expects($this->once())->method('setRedirect');
        $this->responseMock->expects($this->once())->method('sendHeaders');
        $bootstrap = $this->getMock('Magento\Framework\App\Bootstrap', [], [], '', false);
        $bootstrap->expects($this->once())->method('isDeveloperMode')->willReturn(false);
        $this->assertTrue($this->http->catchException(
            $bootstrap,
            new \Magento\Framework\Exception\SessionException(new \Magento\Framework\Phrase('Test'))
        ));
    }

    /**
     * Prepares a mock of bootstrap in "not installed" state
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getBootstrapNotInstalled()
    {
        $bootstrap = $this->getMock('Magento\Framework\App\Bootstrap', [], [], '', false);
        $bootstrap->expects($this->once())->method('isDeveloperMode')->willReturn(true);
        $bootstrap->expects($this->once())->method('getErrorCode')->willReturn(Bootstrap::ERR_IS_INSTALLED);
        return $bootstrap;
    }
}
