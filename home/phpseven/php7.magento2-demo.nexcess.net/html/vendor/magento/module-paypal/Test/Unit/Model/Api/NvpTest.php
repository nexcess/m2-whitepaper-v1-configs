<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Paypal\Test\Unit\Model\Api;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NvpTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Paypal\Model\Api\Nvp */
    protected $model;

    /** @var \Magento\Customer\Helper\Address|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerAddressHelper;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $logger;

    /** @var \Magento\Framework\Locale\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $resolver;

    /** @var \Magento\Directory\Model\RegionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $regionFactory;

    /** @var \Magento\Directory\Model\CountryFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $countryFactory;

    /** @var \Magento\Paypal\Model\Api\ProcessableException|\PHPUnit_Framework_MockObject_MockObject */
    protected $processableException;

    /** @var \Magento\Framework\Exception\LocalizedException|\PHPUnit_Framework_MockObject_MockObject */
    protected $exception;

    /** @var \Magento\Framework\HTTP\Adapter\Curl|\PHPUnit_Framework_MockObject_MockObject */
    protected $curl;

    /** @var \Magento\Paypal\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $config;

    /** @var \Magento\Payment\Model\Method\Logger|\PHPUnit_Framework_MockObject_MockObject */
    protected $customLoggerMock;

    protected function setUp()
    {
        $this->customerAddressHelper = $this->getMock('Magento\Customer\Helper\Address', [], [], '', false);
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');
        $this->customLoggerMock = $this->getMockBuilder('\Magento\Payment\Model\Method\Logger')
            ->setConstructorArgs([$this->getMockForAbstractClass('Psr\Log\LoggerInterface')])
            ->setMethods(['debug'])
            ->getMock();
        $this->resolver = $this->getMock('Magento\Framework\Locale\ResolverInterface');
        $this->regionFactory = $this->getMock('Magento\Directory\Model\RegionFactory', [], [], '', false);
        $this->countryFactory = $this->getMock('Magento\Directory\Model\CountryFactory', [], [], '', false);
        $processableExceptionFactory = $this->getMock(
            'Magento\Paypal\Model\Api\ProcessableExceptionFactory',
            ['create'],
            [],
            '',
            false
        );
        $processableExceptionFactory->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(function ($arguments) {
                $this->processableException = $this->getMock(
                    'Magento\Paypal\Model\Api\ProcessableException',
                    null,
                    [$arguments['phrase'], null, $arguments['code']]
                );
                return $this->processableException;
            }));
        $exceptionFactory = $this->getMock(
            'Magento\Framework\Exception\LocalizedExceptionFactory',
            ['create'],
            [],
            '',
            false
        );
        $exceptionFactory->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(function ($arguments) {
                $this->exception = $this->getMock(
                    'Magento\Framework\Exception\LocalizedException',
                    null,
                    [$arguments['phrase']]
                );
                return $this->exception;
            }));
        $this->curl = $this->getMock('Magento\Framework\HTTP\Adapter\Curl', [], [], '', false);
        $curlFactory = $this->getMock('Magento\Framework\HTTP\Adapter\CurlFactory', ['create'], [], '', false);
        $curlFactory->expects($this->any())->method('create')->will($this->returnValue($this->curl));
        $this->config = $this->getMock('Magento\Paypal\Model\Config', [], [], '', false);

        $helper = new ObjectManagerHelper($this);
        $this->model = $helper->getObject(
            'Magento\Paypal\Model\Api\Nvp',
            [
                'customerAddress' => $this->customerAddressHelper,
                'logger' => $this->logger,
                'customLogger' => $this->customLoggerMock,
                'localeResolver' => $this->resolver,
                'regionFactory' => $this->regionFactory,
                'countryFactory' => $this->countryFactory,
                'processableExceptionFactory' => $processableExceptionFactory,
                'frameworkExceptionFactory' => $exceptionFactory,
                'curlFactory' => $curlFactory,
            ]
        );
        $this->model->setConfigObject($this->config);
    }

    /**
     * @param \Magento\Paypal\Model\Api\Nvp $nvpObject
     * @param string $property
     * @return mixed
     */
    protected function _invokeNvpProperty(\Magento\Paypal\Model\Api\Nvp $nvpObject, $property)
    {
        $object = new \ReflectionClass($nvpObject);
        $property = $object->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($nvpObject);
    }

    /**
     * @param string $response
     * @param array $processableErrors
     * @param null|string $exception
     * @param string $exceptionMessage
     * @param null|int $exceptionCode
     * @dataProvider callDataProvider
     */
    public function testCall($response, $processableErrors, $exception, $exceptionMessage = '', $exceptionCode = null)
    {
        if (isset($exception)) {
            $this->setExpectedException($exception, $exceptionMessage, $exceptionCode);
        }
        $this->curl->expects($this->once())
            ->method('read')
            ->will($this->returnValue($response));
        $this->model->setProcessableErrors($processableErrors);
        $this->customLoggerMock->expects($this->once())
            ->method('debug');
        $this->model->call('some method', ['data' => 'some data']);
    }

    public function callDataProvider()
    {
        return [
            ['', [], null],
            [
                "\r\n" . 'ACK=Failure&L_ERRORCODE0=10417&L_SHORTMESSAGE0=Message.&L_LONGMESSAGE0=Long%20Message.',
                [],
                'Magento\Framework\Exception\LocalizedException',
                'PayPal gateway has rejected request. Long Message (#10417: Message).',
                0
            ],
            [
                "\r\n" . 'ACK=Failure&L_ERRORCODE0=10417&L_SHORTMESSAGE0=Message.&L_LONGMESSAGE0=Long%20Message.',
                [10417, 10422],
                'Magento\Paypal\Model\Api\ProcessableException',
                'PayPal gateway has rejected request. Long Message (#10417: Message).',
                10417
            ],
            [
                "\r\n" . 'ACK[7]=Failure&L_ERRORCODE0[5]=10417'
                    . '&L_SHORTMESSAGE0[8]=Message.&L_LONGMESSAGE0[15]=Long%20Message.',
                [10417, 10422],
                'Magento\Paypal\Model\Api\ProcessableException',
                'PayPal gateway has rejected request. Long Message (#10417: Message).',
                10417
            ],
            [
                "\r\n" . 'ACK[7]=Failure&L_ERRORCODE0[5]=10417&L_SHORTMESSAGE0[8]=Message.',
                [10417, 10422],
                'Magento\Paypal\Model\Api\ProcessableException',
                'PayPal gateway has rejected request. #10417: Message.',
                10417
            ],
        ];
    }

    public function testCallGetExpressCheckoutDetails()
    {
        $this->curl->expects($this->once())
            ->method('read')
            ->will($this->returnValue(
                "\r\n" . 'ACK=Success&SHIPTONAME=Ship%20To%20Name'
            ));
        $this->model->callGetExpressCheckoutDetails();
        $this->assertEquals('Ship To Name', $this->model->getExportedShippingAddress()->getData('firstname'));
    }

    public function testGetDebugReplacePrivateDataKeys()
    {
        $debugReplacePrivateDataKeys = $this->_invokeNvpProperty($this->model, '_debugReplacePrivateDataKeys');
        $this->assertEquals($debugReplacePrivateDataKeys, $this->model->getDebugReplacePrivateDataKeys());
    }
}
