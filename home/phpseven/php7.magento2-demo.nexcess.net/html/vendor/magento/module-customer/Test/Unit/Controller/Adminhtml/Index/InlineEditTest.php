<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Controller\Adminhtml\Index;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class InlineEditTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Controller\Adminhtml\Index\InlineEdit */
    protected $controller;

    /** @var \Magento\Backend\App\Action\Context */
    protected $context;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject*/
    protected $request;

    /** @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject*/
    protected $messageManager;

    /** @var \Magento\Customer\Api\Data\CustomerInterface|\PHPUnit_Framework_MockObject_MockObject*/
    protected $customerData;

    /** @var \Magento\Customer\Api\Data\AddressInterface|\PHPUnit_Framework_MockObject_MockObject*/
    protected $address;

    /** @var \Magento\Framework\Controller\Result\JsonFactory|\PHPUnit_Framework_MockObject_MockObject*/
    protected $resultJsonFactory;

    /** @var \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject*/
    protected $resultJson;

    /** @var \Magento\Customer\Api\CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject*/
    protected $customerRepository;

    /** @var \Magento\Customer\Model\Address\Mapper|\PHPUnit_Framework_MockObject_MockObject*/
    protected $addressMapper;

    /** @var \Magento\Customer\Model\Customer\Mapper|\PHPUnit_Framework_MockObject_MockObject*/
    protected $customerMapper;

    /** @var \Magento\Framework\Api\DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject*/
    protected $dataObjectHelper;

    /** @var \Magento\Customer\Api\Data\AddressInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject*/
    protected $addressDataFactory;

    /** @var \Magento\Customer\Api\AddressRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject*/
    protected $addressRepository;

    /** @var \Magento\Framework\Message\Collection|\PHPUnit_Framework_MockObject_MockObject*/
    protected $messageCollection;

    /** @var \Magento\Framework\Message\MessageInterface|\PHPUnit_Framework_MockObject_MockObject*/
    protected $message;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject*/
    protected $logger;

    /** @var array */
    protected $items;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface', [], '', false);
        $this->messageManager = $this->getMockForAbstractClass(
            'Magento\Framework\Message\ManagerInterface',
            [],
            '',
            false
        );
        $this->customerData = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\CustomerInterface',
            [],
            '',
            false
        );
        $this->address = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressInterface',
            [],
            'address',
            false
        );
        $this->addressMapper = $this->getMock('Magento\Customer\Model\Address\Mapper', [], [], '', false);
        $this->customerMapper = $this->getMock('Magento\Customer\Model\Customer\Mapper', [], [], '', false);
        $this->resultJsonFactory = $this->getMock(
            'Magento\Framework\Controller\Result\JsonFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resultJson = $this->getMock('Magento\Framework\Controller\Result\Json', [], [], '', false);
        $this->customerRepository = $this->getMockForAbstractClass(
            'Magento\Customer\Api\CustomerRepositoryInterface',
            [],
            '',
            false
        );
        $this->dataObjectHelper = $this->getMock('Magento\Framework\Api\DataObjectHelper', [], [], '', false);
        $this->addressDataFactory = $this->getMock(
            'Magento\Customer\Api\Data\AddressInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->addressRepository = $this->getMockForAbstractClass(
            'Magento\Customer\Api\AddressRepositoryInterface',
            [],
            '',
            false
        );
        $this->messageCollection = $this->getMock('Magento\Framework\Message\Collection', [], [], '', false);
        $this->message = $this->getMockForAbstractClass(
            'Magento\Framework\Message\MessageInterface',
            [],
            '',
            false
        );
        $this->logger = $this->getMockForAbstractClass('Psr\Log\LoggerInterface', [], '', false);

        $this->context = $objectManager->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $this->request,
                'messageManager' => $this->messageManager,
            ]
        );
        $this->controller = $objectManager->getObject(
            'Magento\Customer\Controller\Adminhtml\Index\InlineEdit',
            [
                'context' => $this->context,
                'resultJsonFactory' => $this->resultJsonFactory,
                'customerRepository' => $this->customerRepository,
                'addressMapper' => $this->addressMapper,
                'customerMapper' => $this->customerMapper,
                'dataObjectHelper' => $this->dataObjectHelper,
                'addressDataFactory' => $this->addressDataFactory,
                'addressRepository' => $this->addressRepository,
                'logger' => $this->logger,
            ]
        );

        $this->items = [
            14 => [
                'email' => 'test@test.ua',
                'billing_postcode' => '07294',
            ]
        ];
    }

    protected function prepareMocksForTesting($populateSequence = 0)
    {
        $this->resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->resultJson);
        $this->request->expects($this->at(0))
            ->method('getParam')
            ->with('items', [])
            ->willReturn($this->items);
        $this->request->expects($this->at(1))
            ->method('getParam')
            ->with('isAjax')
            ->willReturn(true);
        $this->customerRepository->expects($this->once())
            ->method('getById')
            ->with(14)
            ->willReturn($this->customerData);
        $this->customerMapper->expects($this->once())
            ->method('toFlatArray')
            ->with($this->customerData)
            ->willReturn(['name' => 'Firstname Lastname']);
        $this->dataObjectHelper->expects($this->at($populateSequence))
            ->method('populateWithArray')
            ->with(
                $this->customerData,
                [
                    'name' => 'Firstname Lastname',
                    'email' => 'test@test.ua',
                ],
                '\Magento\Customer\Api\Data\CustomerInterface'
            );
        $this->customerData->expects($this->any())
            ->method('getId')
            ->willReturn(12);
    }

    protected function prepareMocksForUpdateDefaultBilling()
    {
        $this->prepareMocksForProcessAddressData();
        $addressData = [
            'postcode' => '07294',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
        ];
        $this->customerData->expects($this->once())
            ->method('getAddresses')
            ->willReturn([$this->address]);
        $this->address->expects($this->once())
            ->method('isDefaultBilling')
            ->willReturn(true);
        $this->dataObjectHelper->expects($this->at(0))
            ->method('populateWithArray')
            ->with(
                $this->address,
                $addressData,
                '\Magento\Customer\Api\Data\AddressInterface'
            );
    }

    protected function prepareMocksForProcessAddressData()
    {
        $this->customerData->expects($this->once())
            ->method('getFirstname')
            ->willReturn('Firstname');
        $this->customerData->expects($this->once())
            ->method('getLastname')
            ->willReturn('Lastname');
    }

    protected function prepareMocksForErrorMessagesProcessing()
    {
        $this->messageManager->expects($this->atLeastOnce())
            ->method('getMessages')
            ->willReturn($this->messageCollection);
        $this->messageCollection->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->message]);
        $this->messageCollection->expects($this->once())
            ->method('getCount')
            ->willReturn(1);
        $this->message->expects($this->once())
            ->method('getText')
            ->willReturn('Error text');
        $this->resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => ['Error text'],
                'error' => true,
            ])
            ->willReturnSelf();
    }

    public function testExecuteWithUpdateBilling()
    {
        $this->prepareMocksForTesting(1);
        $this->customerData->expects($this->once())
            ->method('getDefaultBilling')
            ->willReturn(23);
        $this->prepareMocksForUpdateDefaultBilling();
        $this->customerRepository->expects($this->once())
            ->method('save')
            ->with($this->customerData);
        $this->prepareMocksForErrorMessagesProcessing();
        $this->assertSame($this->resultJson, $this->controller->execute());
    }

    public function testExecuteWithoutItems()
    {
        $this->resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->resultJson);
        $this->request->expects($this->at(0))
            ->method('getParam')
            ->with('items', [])
            ->willReturn([]);
        $this->request->expects($this->at(1))
            ->method('getParam')
            ->with('isAjax')
            ->willReturn(false);
        $this->resultJson
            ->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ])
            ->willReturnSelf();
        $this->assertSame($this->resultJson, $this->controller->execute());
    }

    public function testExecuteLocalizedException()
    {
        $exception = new \Magento\Framework\Exception\LocalizedException(__('Exception message'));
        $this->prepareMocksForTesting();
        $this->customerData->expects($this->once())
            ->method('getDefaultBilling')
            ->willReturn(false);
        $this->customerRepository->expects($this->once())
            ->method('save')
            ->with($this->customerData)
            ->willThrowException($exception);
        $this->messageManager->expects($this->once())
            ->method('addError')
            ->with('[Customer ID: 12] Exception message');
        $this->logger->expects($this->once())
            ->method('critical')
            ->with($exception);

        $this->prepareMocksForErrorMessagesProcessing();
        $this->assertSame($this->resultJson, $this->controller->execute());
    }

    public function testExecuteException()
    {
        $exception = new \Exception('Exception message');
        $this->prepareMocksForTesting();
        $this->customerData->expects($this->once())
            ->method('getDefaultBilling')
            ->willReturn(false);
        $this->customerRepository->expects($this->once())
            ->method('save')
            ->with($this->customerData)
            ->willThrowException($exception);
        $this->messageManager->expects($this->once())
            ->method('addError')
            ->with('[Customer ID: 12] We can\'t save the customer.');
        $this->logger->expects($this->once())
            ->method('critical')
            ->with($exception);

        $this->prepareMocksForErrorMessagesProcessing();
        $this->assertSame($this->resultJson, $this->controller->execute());
    }
}
