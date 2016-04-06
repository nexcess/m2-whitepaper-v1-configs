<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Message\Test\Unit;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Message\CollectionFactory;
use Magento\Framework\Message\Factory;
use Magento\Framework\Message\Manager;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Message\Session;
use Psr\Log\LoggerInterface;

/**
 * \Magento\Framework\Message\Manager test case
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageFactory;

    /**
     * @var CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messagesFactory;

    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * @var Manager
     */
    protected $model;

    /**
     * @var MessageInterface |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageMock;

    /**
     * @var LoggerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->messagesFactory = $this->getMockBuilder(
            'Magento\Framework\Message\CollectionFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageFactory = $this->getMockBuilder(
            'Magento\Framework\Message\Factory'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->session = $this->getMockBuilder(
            'Magento\Framework\Message\Session'
        )
            ->disableOriginalConstructor()
            ->setMethods(
                ['getData', 'setData']
            )
            ->getMock();
        $this->eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');

        $this->messageMock = $this->getMock('Magento\Framework\Message\MessageInterface');
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = new Manager(
            $this->session,
            $this->messageFactory,
            $this->messagesFactory,
            $this->eventManager,
            $this->logger
        );
    }

    public function testGetDefaultGroup()
    {
        $this->assertEquals(Manager::DEFAULT_GROUP, $this->model->getDefaultGroup());
    }

    public function testGetMessages()
    {
        $messageCollection = $this->getMockBuilder(
            'Magento\Framework\Message\Collection'
        )->disableOriginalConstructor()->setMethods(
            ['addMessage']
        )->getMock();

        $this->messagesFactory->expects(
            $this->atLeastOnce()
        )->method(
            'create'
        )->will(
            $this->returnValue($messageCollection)
        );

        $this->session->expects(
            $this->at(0)
        )->method(
            'getData'
        )->with(
            Manager::DEFAULT_GROUP
        )->will(
            $this->returnValue(null)
        );
        $this->session->expects(
            $this->at(1)
        )->method(
            'setData'
        )->with(
            Manager::DEFAULT_GROUP,
            $messageCollection
        )->will(
            $this->returnValue($this->session)
        );
        $this->session->expects(
            $this->at(2)
        )->method(
            'getData'
        )->with(
            Manager::DEFAULT_GROUP
        )->will(
            $this->returnValue($messageCollection)
        );

        $this->eventManager->expects($this->never())->method('dispatch');

        $this->assertEquals($messageCollection, $this->model->getMessages());
    }

    public function testGetMessagesWithClear()
    {
        $messageCollection = $this->getMockBuilder(
            'Magento\Framework\Message\Collection'
        )->disableOriginalConstructor()->setMethods(
            ['addMessage', 'clear']
        )->getMock();

        $messageCollection->expects($this->once())->method('clear');

        $this->session->expects(
            $this->any()
        )->method(
            'getData'
        )->with(
            Manager::DEFAULT_GROUP
        )->will(
            $this->returnValue($messageCollection)
        );

        $this->eventManager->expects($this->once())->method('dispatch')->with('session_abstract_clear_messages');

        $this->assertEquals($messageCollection, $this->model->getMessages(true));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testAddException()
    {
        $exceptionMessage = 'exception message';
        $alternativeText = 'alternative text';
        $logText = "Exception message: {$exceptionMessage}\nTrace:";

        $messageError = $this->getMockBuilder(
            'Magento\Framework\Message\Error'
        )->setConstructorArgs(
            ['text' => $alternativeText]
        )->getMock();

        $this->messageFactory->expects(
            $this->atLeastOnce()
        )->method(
            'create'
        )->with(
            MessageInterface::TYPE_ERROR,
            $alternativeText
        )->will(
            $this->returnValue($messageError)
        );

        $messageCollection = $this->getMockBuilder(
            'Magento\Framework\Message\Collection'
        )->disableOriginalConstructor()->setMethods(
            ['addMessage']
        )->getMock();
        $messageCollection->expects($this->atLeastOnce())->method('addMessage')->with($messageError);

        $this->session->expects(
            $this->atLeastOnce()
        )->method(
            'getData'
        )->with(
            Manager::DEFAULT_GROUP
        )->will(
            $this->returnValue($messageCollection)
        );

        $exception = new \Exception($exceptionMessage);
        $this->assertEquals($this->model, $this->model->addException($exception, $alternativeText));
    }

    /**
     * @param string $type
     * @param string $methodName
     * @dataProvider addMessageDataProvider
     */
    public function testAddMessage($type, $methodName)
    {
        $this->assertFalse($this->model->hasMessages());
        $message = 'Message';
        $messageCollection = $this->getMock(
            'Magento\Framework\Message\Collection',
            ['addMessage'],
            [],
            '',
            false
        );
        $this->session->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($messageCollection));
        $this->eventManager->expects($this->once())
            ->method('dispatch')->with('session_abstract_add_message');
        $this->messageFactory->expects($this->once())
            ->method('create')->with($type, $message)
            ->will($this->returnValue($this->messageMock));
        $this->model->$methodName($message, 'group');
        $this->assertTrue($this->model->hasMessages());
    }

    public function addMessageDataProvider()
    {
        return [
            'error' => [MessageInterface::TYPE_ERROR, 'addError'],
            'warning' => [MessageInterface::TYPE_WARNING, 'addWarning'],
            'notice' => [MessageInterface::TYPE_NOTICE, 'addNotice'],
            'success' => [MessageInterface::TYPE_SUCCESS, 'addSuccess']
        ];
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $messages
     * @param string $expectation
     * @dataProvider addUniqueMessagesWhenMessagesImplementMessageInterfaceDataProvider
     */
    public function testAddUniqueMessagesWhenMessagesImplementMessageInterface($messages, $expectation)
    {
        $messageCollection =
            $this->getMock('Magento\Framework\Message\Collection', ['getItems', 'addMessage'], [], '', false);
        $this->session->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($messageCollection));
        $messageCollection
            ->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue([new TestingMessage('text')]));
        $messageCollection->expects($this->$expectation())->method('addMessage');
        $this->model->addUniqueMessages([$messages]);
    }

    public function addUniqueMessagesWhenMessagesImplementMessageInterfaceDataProvider()
    {
        return [
            'message_text_is_unique' => [
                new TestingMessage('text1'),
                'once',
            ],
            'message_text_already_exists' => [
                new TestingMessage('text'),
                'never',
            ]
        ];
    }

    /**
     * @param string|array $messages
     * @dataProvider addUniqueMessagesDataProvider
     */
    public function testAddUniqueMessages($messages)
    {
        $messageCollection =
            $this->getMock('Magento\Framework\Message\Collection', ['getItems', 'addMessage'], [], '', false);
        $this->session->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($messageCollection));
        $messageCollection
            ->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue(['message']));
        $messageCollection->expects($this->never())->method('addMessage');
        $this->model->addUniqueMessages($messages);
    }

    public function addUniqueMessagesDataProvider()
    {
        return [
            'messages_are_text' => [['message']],
            'messages_are_empty' => [[]]
        ];
    }

    public function testAddMessages()
    {
        $messageCollection = $this->getMock(
            'Magento\Framework\Message\Collection',
            ['getItems', 'addMessage'],
            [],
            '',
            false
        );
        $this->session->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($messageCollection));
        $this->eventManager->expects($this->once())
            ->method('dispatch')->with('session_abstract_add_message');

        $messageCollection->expects($this->once())->method('addMessage')->with($this->messageMock);
        $this->model->addMessages([$this->messageMock]);
    }
}
