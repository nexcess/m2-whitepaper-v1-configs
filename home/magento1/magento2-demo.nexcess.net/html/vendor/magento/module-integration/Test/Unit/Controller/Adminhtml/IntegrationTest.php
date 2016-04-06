<?php
/**
 * \Magento\Integration\Controller\Adminhtml
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Integration\Test\Unit\Controller\Adminhtml;

use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;
use Magento\Integration\Model\Integration as IntegrationModel;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Integration\Controller\Adminhtml\Integration */
    protected $_controller;

    /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  $objectManagerHelper */
    protected $_objectManagerHelper;

    /** @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    /** @var \Magento\Backend\Model\View\Layout\Filter\Acl|\PHPUnit_Framework_MockObject_MockObject */
    protected $_layoutFilterMock;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_configMock;

    /** @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_eventManagerMock;

    /** @var \Magento\Framework\Translate|\PHPUnit_Framework_MockObject_MockObject */
    protected $_translateModelMock;

    /** @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $_backendSessionMock;

    /** @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $_backendActionCtxMock;

    /** @var \Magento\Integration\Api\IntegrationServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_integrationSvcMock;

    /** @var \Magento\Integration\Api\OauthServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_oauthSvcMock;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $_registryMock;

    /** @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject */
    protected $_requestMock;

    /** @var \Magento\Framework\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject */
    protected $_responseMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $_messageManager;

    /** @var \Magento\Framework\Config\ScopeInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_configScopeMock;

    /** @var \Magento\Integration\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $_integrationHelperMock;

    /** @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_viewMock;

    /** @var \Magento\Framework\View\Model\Layout\Merge|\PHPUnit_Framework_MockObject_MockObject */
    protected $_layoutMergeMock;

    /** @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_layoutMock;

    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewConfigMock;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageTitleMock;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_escaper;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactory;

    /** Sample integration ID */
    const INTEGRATION_ID = 1;

    /**
     * Setup object manager and initialize mocks
     */
    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  $objectManagerHelper */
        $this->_objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        // Initialize mocks which are used in several test cases
        $this->_configMock = $this->getMockBuilder(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_eventManagerMock = $this->getMockBuilder(
            'Magento\Framework\Event\ManagerInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_layoutFilterMock = $this->getMockBuilder(
            'Magento\Backend\Model\Layout\Filter\Acl'
        )->disableOriginalConstructor()->getMock();
        $this->_backendSessionMock = $this->getMockBuilder(
            'Magento\Backend\Model\Session'
        )->disableOriginalConstructor()->getMock();
        $this->_translateModelMock = $this->getMockBuilder(
            'Magento\Framework\TranslateInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_integrationSvcMock = $this->getMockBuilder(
            'Magento\Integration\Api\IntegrationServiceInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_oauthSvcMock = $this->getMockBuilder(
            'Magento\Integration\Api\OauthServiceInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_requestMock = $this->getMockBuilder(
            'Magento\Framework\App\Request\Http'
        )->disableOriginalConstructor()->getMock();
        $this->_responseMock = $this->getMockBuilder(
            'Magento\Framework\App\Response\Http'
        )->disableOriginalConstructor()->getMock();
        $this->_registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_configScopeMock = $this->getMockBuilder(
            'Magento\Framework\Config\ScopeInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_integrationHelperMock = $this->getMockBuilder(
            'Magento\Integration\Helper\Data'
        )->disableOriginalConstructor()->getMock();
        $this->_messageManager = $this->getMockBuilder(
            'Magento\Framework\Message\ManagerInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_escaper = $this->getMockBuilder(
            'Magento\Framework\Escaper'
        )->setMethods(
            ['escapeHtml']
        )->disableOriginalConstructor()->getMock();
        $this->resultPageMock = $this->getMockBuilder('Magento\Framework\View\Result\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTitleMock = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param string $actionName
     * @return \Magento\Integration\Controller\Adminhtml\Integration
     */
    protected function _createIntegrationController($actionName)
    {
        // Mock Layout passed into constructor
        $this->_viewMock = $this->getMockBuilder('Magento\Framework\App\ViewInterface')
            ->getMock();
        $this->_layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface');
        $this->_layoutMergeMock = $this->getMockBuilder(
            'Magento\Framework\View\Model\Layout\Merge'
        )->disableOriginalConstructor()->getMock();
        $this->_layoutMock->expects(
            $this->any()
        )->method(
            'getUpdate'
        )->will(
            $this->returnValue($this->_layoutMergeMock)
        );
        $testElement = new \Magento\Framework\Simplexml\Element('<test>test</test>');
        $this->_layoutMock->expects($this->any())->method('getNode')->will($this->returnValue($testElement));
        // for _setActiveMenu
        $this->_viewMock->expects($this->any())->method('getLayout')->will($this->returnValue($this->_layoutMock));
        $blockMock = $this->getMockBuilder('Magento\Backend\Block\Menu')->disableOriginalConstructor()->getMock();
        $menuMock = $this->getMock(
            'Magento\Backend\Model\Menu',
            [],
            [$this->getMock('Psr\Log\LoggerInterface')]
        );
        $loggerMock = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();
        $loggerMock->expects($this->any())->method('critical')->will($this->returnSelf());
        $menuMock->expects($this->any())->method('getParentItems')->will($this->returnValue([]));
        $blockMock->expects($this->any())->method('getMenuModel')->will($this->returnValue($menuMock));
        $this->_layoutMock->expects($this->any())->method('getMessagesBlock')->will($this->returnValue($blockMock));
        $this->_layoutMock->expects($this->any())->method('getBlock')->will($this->returnValue($blockMock));
        $this->_viewMock->expects($this->any())
            ->method('getPage')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->viewConfigMock);
        $this->viewConfigMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);
        $this->_escaper->expects($this->any())->method('escapeHtml')->will($this->returnArgument(0));

        $this->resultRedirectFactory = $this->getMockBuilder('Magento\Backend\Model\View\Result\RedirectFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->resultFactory = $this->getMockBuilder('Magento\Framework\Controller\ResultFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $contextParameters = [
            'view' => $this->_viewMock,
            'objectManager' => $this->_objectManagerMock,
            'session' => $this->_backendSessionMock,
            'translator' => $this->_translateModelMock,
            'request' => $this->_requestMock,
            'response' => $this->_responseMock,
            'messageManager' => $this->_messageManager,
            'resultRedirectFactory' => $this->resultRedirectFactory,
            'resultFactory' => $this->resultFactory
        ];

        $this->_backendActionCtxMock = $this->_objectManagerHelper->getObject(
            'Magento\Backend\App\Action\Context',
            $contextParameters
        );

        $integrationCollection =
            $this->getMockBuilder('\Magento\Integration\Model\ResourceModel\Integration\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['addUnsecureUrlsFilter', 'getSize'])
            ->getMock();
        $integrationCollection->expects($this->any())
            ->method('addUnsecureUrlsFilter')
            ->will($this->returnValue($integrationCollection));
        $integrationCollection->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(0));

        $subControllerParams = [
            'context' => $this->_backendActionCtxMock,
            'integrationService' => $this->_integrationSvcMock,
            'oauthService' => $this->_oauthSvcMock,
            'registry' => $this->_registryMock,
            'logger' => $loggerMock,
            'integrationData' => $this->_integrationHelperMock,
            'escaper' => $this->_escaper,
            'integrationCollection' => $integrationCollection,
        ];
        /** Create IntegrationController to test */
        $controller = $this->_objectManagerHelper->getObject(
            '\\Magento\\Integration\\Controller\\Adminhtml\\Integration\\' . $actionName,
            $subControllerParams
        );
        return $controller;
    }

    /**
     * Common mock 'expect' pattern.
     * Calls that need to be mocked out when
     * \Magento\Backend\App\AbstractAction loadLayout() and renderLayout() are called.
     */
    protected function _verifyLoadAndRenderLayout()
    {
        $map = [
            ['Magento\Framework\App\Config\ScopeConfigInterface', $this->_configMock],
            ['Magento\Backend\Model\Layout\Filter\Acl', $this->_layoutFilterMock],
            ['Magento\Backend\Model\Session', $this->_backendSessionMock],
            ['Magento\Framework\TranslateInterface', $this->_translateModelMock],
            ['Magento\Framework\Config\ScopeInterface', $this->_configScopeMock],
        ];
        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValueMap($map));
    }

    /**
     * Return sample Integration Data
     *
     * @return \Magento\Framework\DataObject
     */
    protected function _getSampleIntegrationData()
    {
        return new \Magento\Framework\DataObject(
            [
                Info::DATA_NAME => 'nameTest',
                Info::DATA_ID => self::INTEGRATION_ID,
                'id' => self::INTEGRATION_ID,
                Info::DATA_EMAIL => 'test@magento.com',
                Info::DATA_ENDPOINT => 'http://magento.ll/endpoint',
                Info::DATA_SETUP_TYPE => IntegrationModel::TYPE_MANUAL,
            ]
        );
    }

    /**
     * Return integration model mock with sample data.
     *
     * @return \Magento\Integration\Model\Integration|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getIntegrationModelMock()
    {
        $integrationModelMock = $this->getMock(
            'Magento\Integration\Model\Integration',
            ['save', '__wakeup'],
            [],
            '',
            false
        );

        $integrationModelMock->expects($this->any())->method('setStatus')->will($this->returnSelf());
        $integrationModelMock->expects(
            $this->any()
        )->method(
            'getData'
        )->will(
            $this->returnValue($this->_getSampleIntegrationData())
        );

        return $integrationModelMock;
    }
}
