<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Integration\Test\Unit\Controller\Token;

class AccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager $objectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\Oauth\OauthInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $frameworkOauthSvcMock;

    /**
     * @var \Magento\Integration\Api\OauthServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $intOauthServiceMock;

    /**
     * @var \Magento\Integration\Api\IntegrationServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $integrationServiceMock;

    /**
     * @var \Magento\Framework\Oauth\Helper\Request|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Integration\Controller\Token\Access
     */
    protected $accessAction;

    protected function setUp()
    {
        $this->request = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            [
                'getMethod',
                'getModuleName',
                'setModuleName',
                'getActionName',
                'setActionName',
                'getParam',
                'setParams',
                'getParams',
                'getCookie',
                'isSecure'
            ],
            [],
            '',
            false
        );
        $this->response = $this->getMock('Magento\Framework\App\Console\Response', [], [], '', false);
        /** @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
        $objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface', [], [], '', false);
        /** @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);

        /** @var \Magento\Framework\View\Layout\ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject */
        $update = $this->getMock('Magento\Framework\View\Layout\ProcessorInterface', [], [], '', false);
        /** @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject */
        $layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $layout->expects($this->any())->method('getUpdate')->will($this->returnValue($update));

        /** @var \Magento\Framework\View\Page\Config */
        $pageConfig = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);
        $pageConfig->expects($this->any())->method('addBodyClass')->will($this->returnSelf());

        /** @var \Magento\Framework\View\Page|\PHPUnit_Framework_MockObject_MockObject */
        $page = $this->getMock(
            'Magento\Framework\View\Page',
            ['getConfig', 'initLayout', 'addPageLayoutHandles', 'getLayout'],
            [],
            '',
            false
        );
        $page->expects($this->any())->method('getConfig')->will($this->returnValue($pageConfig));
        $page->expects($this->any())->method('addPageLayoutHandles')->will($this->returnSelf());
        $page->expects($this->any())->method('getLayout')->will($this->returnValue($layout));

        /** @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject */
        $view = $this->getMock('Magento\Framework\App\ViewInterface', [], [], '', false);
        $view->expects($this->any())->method('getLayout')->will($this->returnValue($layout));

        /** @var Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject */
        $resultFactory = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);
        $resultFactory->expects($this->any())->method('create')->will($this->returnValue($page));

        $this->context = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $this->context->expects($this->any())->method('getRequest')->will($this->returnValue($this->request));
        $this->context->expects($this->any())->method('getResponse')->will($this->returnValue($this->response));
        $this->context->expects($this->any())->method('getObjectManager')
            ->will($this->returnValue($objectManager));
        $this->context->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));
        $this->context->expects($this->any())->method('getView')->will($this->returnValue($view));
        $this->context->expects($this->any())->method('getResultFactory')
            ->will($this->returnValue($resultFactory));

        $this->helperMock = $this->getMock('Magento\Framework\Oauth\Helper\Request', [], [], '', false);
        $this->frameworkOauthSvcMock = $this->getMock('Magento\Framework\Oauth\OauthInterface', [], [], '', false);
        $this->intOauthServiceMock = $this->getMock('Magento\Integration\Api\OauthServiceInterface', [], [], '', false);
        $this->integrationServiceMock = $this->getMock(
            'Magento\Integration\Api\IntegrationServiceInterface',
            [],
            [],
            '',
            false
        );

        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager $objectManagerHelper */
        $this->objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->accessAction = $this->objectManagerHelper->getObject(
            'Magento\Integration\Controller\Token\Access',
            [
                'context' => $this->context,
                'oauthService'=> $this->frameworkOauthSvcMock,
                'intOauthService' => $this->intOauthServiceMock,
                'integrationService' => $this->integrationServiceMock,
                'helper' => $this->helperMock,
            ]
        );
    }

    /**
     * Test the basic Access action.
     */
    public function testAccessAction()
    {
        $this->request->expects($this->any())
            ->method('getMethod')
            ->willReturn('GET');
        $this->helperMock->expects($this->once())
            ->method('getRequestUrl');
        $this->helperMock->expects($this->once())
            ->method('prepareRequest');
        $this->frameworkOauthSvcMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn(['response']);
        /** @var \Magento\Integration\Model\Oauth\Consumer|\PHPUnit_Framework_MockObject_MockObject */
        $consumerMock = $this->getMock('Magento\Integration\Model\Oauth\Consumer', [], [], '', false);
        $consumerMock->expects($this->once())
            ->method('getId');
        $this->intOauthServiceMock->expects($this->once())
            ->method('loadConsumerByKey')
            ->willReturn($consumerMock);
        /** @var \Magento\Integration\Model\Integration|\PHPUnit_Framework_MockObject_MockObject */
        $integrationMock = $this->getMock('Magento\Integration\Model\Integration', [], [], '', false);
        $integrationMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->integrationServiceMock->expects($this->once())
            ->method('findByConsumerId')
            ->willReturn($integrationMock);
        $this->response->expects($this->once())
            ->method('setBody');

        $this->accessAction->execute();
    }
}
