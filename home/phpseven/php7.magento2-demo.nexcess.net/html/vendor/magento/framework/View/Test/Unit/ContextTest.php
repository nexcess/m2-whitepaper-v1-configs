<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test for view Context model
 */
namespace Magento\Framework\View\Test\Unit;

use \Magento\Framework\View\Context;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appState;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $design;

    protected function setUp()
    {
        $this->appState = $this->getMockBuilder('Magento\Framework\App\State')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->design = $this->getMockBuilder('Magento\Framework\View\DesignInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->context = $objectManager->getObject('Magento\Framework\View\Context', [
            'appState' => $this->appState,
            'request' => $this->request,
            'design' => $this->design
        ]);
    }

    public function testGetCache()
    {
        $this->assertInstanceOf('\Magento\Framework\App\CacheInterface', $this->context->getCache());
    }

    public function testGetDesignPackage()
    {
        $this->assertInstanceOf('\Magento\Framework\View\DesignInterface', $this->context->getDesignPackage());
    }

    public function testGetEventManager()
    {
        $this->assertInstanceOf('\Magento\Framework\Event\ManagerInterface', $this->context->getEventManager());
    }

    public function testGetFrontController()
    {
        $this->assertInstanceOf(
            '\Magento\Framework\App\FrontControllerInterface',
            $this->context->getFrontController()
        );
    }

    public function testGetLayout()
    {
        $this->assertInstanceOf('\Magento\Framework\View\LayoutInterface', $this->context->getLayout());
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf('\Magento\Framework\App\Request\Http', $this->context->getRequest());
    }

    public function testGetSession()
    {
        $this->assertInstanceOf('\Magento\Framework\Session\SessionManagerInterface', $this->context->getSession());
    }

    public function testGetScopeConfig()
    {
        $this->assertInstanceOf('\Magento\Framework\App\Config\ScopeConfigInterface', $this->context->getScopeConfig());
    }

    public function testGetTranslator()
    {
        $this->assertInstanceOf('\Magento\Framework\TranslateInterface', $this->context->getTranslator());
    }

    public function testGetUrlBuilder()
    {
        $this->assertInstanceOf('\Magento\Framework\UrlInterface', $this->context->getUrlBuilder());
    }

    public function testGetViewConfig()
    {
        $this->assertInstanceOf('\Magento\Framework\View\ConfigInterface', $this->context->getViewConfig());
    }

    public function testGetCacheState()
    {
        $this->assertInstanceOf('\Magento\Framework\App\Cache\StateInterface', $this->context->getCacheState());
    }

    public function testGetLogger()
    {
        $this->assertInstanceOf('\Psr\Log\LoggerInterface', $this->context->getLogger());
    }

    public function testGetAppState()
    {
        $this->assertInstanceOf('\Magento\Framework\App\State', $this->context->getAppState());
    }

    public function testGetArea()
    {
        $area = 'frontendArea';

        $this->appState->expects($this->once())
            ->method('getAreaCode')
            ->will($this->returnValue($area));

        $this->assertEquals($area, $this->context->getArea());
    }

    public function testGetModuleName()
    {
        $moduleName = 'testModuleName';

        $this->request->expects($this->once())
            ->method('getModuleName')
            ->will($this->returnValue($moduleName));

        $this->assertEquals($moduleName, $this->context->getModuleName());
    }

    public function testGetFrontName()
    {
        $frontName = 'testFrontName';

        $this->request->expects($this->once())
            ->method('getModuleName')
            ->will($this->returnValue($frontName));

        $this->assertEquals($frontName, $this->context->getFrontName());
    }

    public function testGetControllerName()
    {
        $controllerName = 'testControllerName';

        $this->request->expects($this->once())
            ->method('getControllerName')
            ->will($this->returnValue($controllerName));

        $this->assertEquals($controllerName, $this->context->getControllerName());
    }

    public function testGetActionName()
    {
        $actionName = 'testActionName';

        $this->request->expects($this->once())
            ->method('getActionName')
            ->will($this->returnValue($actionName));

        $this->assertEquals($actionName, $this->context->getActionName());
    }

    public function testGetFullActionName()
    {
        $frontName = 'testFrontName';
        $controllerName = 'testControllerName';
        $actionName = 'testActionName';
        $fullActionName = 'testfrontname_testcontrollername_testactionname';

        $this->request->expects($this->once())
            ->method('getModuleName')
            ->will($this->returnValue($frontName));

        $this->request->expects($this->once())
            ->method('getControllerName')
            ->will($this->returnValue($controllerName));

        $this->request->expects($this->once())
            ->method('getActionName')
            ->will($this->returnValue($actionName));

        $this->assertEquals($fullActionName, $this->context->getFullActionName());
    }

    /**
     * @param string $headerAccept
     * @param string $acceptType
     *
     * @dataProvider getAcceptTypeDataProvider
     */
    public function testGetAcceptType($headerAccept, $acceptType)
    {
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Accept')
            ->will($this->returnValue($headerAccept));

        $this->assertEquals($acceptType, $this->context->getAcceptType());
    }

    public function getAcceptTypeDataProvider()
    {
        return [
            ['json', 'json'],
            ['testjson', 'json'],
            ['soap', 'soap'],
            ['testsoap', 'soap'],
            ['text/html', 'html'],
            ['testtext/html', 'html'],
            ['xml', 'xml'],
            ['someElse', 'xml'],
        ];
    }

    public function testGetPost()
    {
        $key = 'getParamName';
        $default = 'defaultGetParamValue';
        $postValue = 'someGetParamValue';

        $this->request->expects($this->once())
            ->method('getPost')
            ->with($key, $default)
            ->will($this->returnValue($postValue));

        $this->assertEquals($postValue, $this->context->getPost($key, $default));
    }

    public function testGetQuery()
    {
        $key = 'getParamName';
        $default = 'defaultGetParamValue';
        $queryValue = 'someGetParamValue';

        $this->request->expects($this->once())
            ->method('getPost')
            ->with($key, $default)
            ->will($this->returnValue($queryValue));

        $this->assertEquals($queryValue, $this->context->getQuery($key, $default));
    }

    public function testGetParam()
    {
        $key = 'paramName';
        $default = 'defaultParamValue';
        $paramValue = 'someParamValue';

        $this->request->expects($this->once())
            ->method('getParam')
            ->with($key, $default)
            ->will($this->returnValue($paramValue));

        $this->assertEquals($paramValue, $this->context->getParam($key, $default));
    }

    public function testGetParams()
    {
        $params = ['paramName' => 'value'];

        $this->request->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue($params));

        $this->assertEquals($params, $this->context->getParams());
    }

    public function testGetHeader()
    {
        $headerName = 'headerName';
        $headerValue = 'headerValue';

        $this->request->expects($this->once())
            ->method('getHeader')
            ->with($headerName)
            ->will($this->returnValue($headerValue));

        $this->assertEquals($headerValue, $this->context->getHeader($headerName));
    }

    public function testContent()
    {
        $content = 'body string';

        $this->request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($content));

        $this->assertEquals($content, $this->context->getContent());
    }
}
