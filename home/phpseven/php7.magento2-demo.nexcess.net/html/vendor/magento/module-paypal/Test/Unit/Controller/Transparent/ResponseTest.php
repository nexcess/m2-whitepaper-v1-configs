<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Test\Unit\Controller\Transparent;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Paypal\Controller\Transparent\Response;
use Magento\Paypal\Model\Payflow\Service\Response\Transaction;
use Magento\Paypal\Model\Payflow\Service\Response\Validator\ResponseValidator;

/**
 * Class ResponseTest
 *
 * Test for class \Magento\Paypal\Controller\Transparent\Response
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Response|\PHPUnit_Framework_MockObject_MockObject */
    private $object;

    /** @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $requestMock;

    /** @var Registry|\PHPUnit_Framework_MockObject_MockObject */
    private $coreRegistryMock;

    /** @var LayoutFactory|\PHPUnit_Framework_MockObject_MockObject */
    private $resultLayoutFactoryMock;

    /** @var Layout|\PHPUnit_Framework_MockObject_MockObject */
    private $resultLayoutMock;

    /** @var Context|\PHPUnit_Framework_MockObject_MockObject */
    private $contextMock;

    /** @var Transaction|\PHPUnit_Framework_MockObject_MockObject */
    private $transactionMock;

    /** @var ResponseValidator|\PHPUnit_Framework_MockObject_MockObject */
    private $responseValidatorMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\RequestInterface')
            ->setMethods(['getPostValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->viewMock = $this->getMockBuilder('Magento\Framework\App\ViewInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->coreRegistryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->setMethods(['register'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultLayoutMock = $this->getMockBuilder('Magento\Framework\View\Result\Layout')
            ->setMethods(['addDefaultHandle', 'getLayout', 'getUpdate', 'load'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultLayoutFactoryMock = $this->getMockBuilder('Magento\Framework\View\Result\LayoutFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultLayoutFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultLayoutMock);
        $this->transactionMock = $this->getMockBuilder(
            'Magento\Paypal\Model\Payflow\Service\Response\Transaction'
        )->setMethods(['getResponseObject', 'validateResponse', 'savePaymentInQuote'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock = $this->getMockBuilder('\Magento\Framework\App\Action\Context')
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->requestMock);
        $this->responseValidatorMock = $this->getMockBuilder(
            'Magento\Paypal\Model\Payflow\Service\Response\Validator\ResponseValidator'
        )->disableOriginalConstructor()
            ->getMock();

        $this->object = new Response(
            $this->contextMock,
            $this->coreRegistryMock,
            $this->transactionMock,
            $this->responseValidatorMock,
            $this->resultLayoutFactoryMock
        );
    }

    public function testExecute()
    {
        $objectMock = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->getMock();
        $this->transactionMock->expects($this->once())
            ->method('getResponseObject')
            ->willReturn($objectMock);
        $this->responseValidatorMock->expects($this->once())
            ->method('validate')
            ->with($objectMock);
        $this->transactionMock->expects($this->once())
            ->method('savePaymentInQuote')
            ->with($objectMock);
        $this->coreRegistryMock->expects($this->once())
            ->method('register')
            ->with('transparent_form_params', $this->logicalNot($this->arrayHasKey('error')));
        $this->resultLayoutMock->expects($this->once())
            ->method('addDefaultHandle')
            ->willReturnSelf();
        $this->resultLayoutMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->getLayoutMock());

        $this->assertInstanceOf('\Magento\Framework\Controller\ResultInterface', $this->object->execute());
    }

    public function testExecuteWithException()
    {
        $objectMock = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->getMock();
        $this->transactionMock->expects($this->once())
            ->method('getResponseObject')
            ->willReturn($objectMock);
        $this->responseValidatorMock->expects($this->once())
            ->method('validate')
            ->with($objectMock)
            ->willThrowException(new \Magento\Framework\Exception\LocalizedException(__('Error')));
        $this->coreRegistryMock->expects($this->once())
            ->method('register')
            ->with('transparent_form_params', $this->arrayHasKey('error'));
        $this->resultLayoutMock->expects($this->once())
            ->method('addDefaultHandle')
            ->willReturnSelf();
        $this->resultLayoutMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->getLayoutMock());

        $this->assertInstanceOf('\Magento\Framework\Controller\ResultInterface', $this->object->execute());
    }

    /**
     * @return \Magento\Framework\View\Layout | \PHPUnit_Framework_MockObject_MockObject
     */
    private function getLayoutMock()
    {
        $processorInterfaceMock = $this->getMockBuilder('\Magento\Framework\View\Layout\ProcessorInterface')
            ->getMockForAbstractClass();
        $layoutMock = $this->getMockBuilder('\Magento\Framework\View\Layout')
            ->setMethods(['getUpdate'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->once())
            ->method('getUpdate')
            ->willReturn($processorInterfaceMock);

        return $layoutMock;
    }
}
