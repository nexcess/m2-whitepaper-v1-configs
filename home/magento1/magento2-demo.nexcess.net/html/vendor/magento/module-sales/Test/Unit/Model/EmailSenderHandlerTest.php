<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test of sales emails sending observer.
 */
class EmailSenderHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Subject of testing.
     *
     * @var \Magento\Sales\Model\EmailSenderHandler
     */
    protected $object;

    /**
     * Email sender model mock.
     *
     * @var \Magento\Sales\Model\Order\Email\Sender|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailSender;

    /**
     * Entity resource model mock.
     *
     * @var \Magento\Sales\Model\ResourceModel\EntityAbstract|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityResource;

    /**
     * Entity collection model mock.
     *
     * @var \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityCollection;

    /**
     * Global configuration storage mock.
     *
     * @var \Magento\Framework\App\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalConfig;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->emailSender = $this->getMock(
            'Magento\Sales\Model\Order\Email\Sender',
            ['send'],
            [],
            '',
            false
        );

        $this->entityResource = $this->getMockForAbstractClass(
            'Magento\Sales\Model\ResourceModel\EntityAbstract',
            [],
            '',
            false,
            false,
            true,
            ['save']
        );

        $this->entityCollection = $this->getMockForAbstractClass(
            'Magento\Sales\Model\ResourceModel\Collection\AbstractCollection',
            [],
            '',
            false,
            false,
            true,
            ['addFieldToFilter', 'getItems']
        );

        $this->globalConfig = $this->getMock(
            'Magento\Framework\App\Config',
            [],
            [],
            '',
            false
        );

        $this->object = $objectManager->getObject(
            'Magento\Sales\Model\EmailSenderHandler',
            [
                'emailSender' => $this->emailSender,
                'entityResource' => $this->entityResource,
                'entityCollection' => $this->entityCollection,
                'globalConfig' => $this->globalConfig
            ]
        );
    }

    /**
     * @param int $configValue
     * @param array|null $collectionItems
     * @param bool|null $emailSendingResult
     * @dataProvider executeDataProvider
     * @return void
     */
    public function testExecute($configValue, $collectionItems, $emailSendingResult)
    {
        $path = 'sales_email/general/async_sending';

        $this->globalConfig
            ->expects($this->once())
            ->method('getValue')
            ->with($path)
            ->willReturn($configValue);

        if ($configValue) {
            $this->entityCollection
                ->expects($this->at(0))
                ->method('addFieldToFilter')
                ->with('send_email', ['eq' => 1]);

            $this->entityCollection
                ->expects($this->at(1))
                ->method('addFieldToFilter')
                ->with('email_sent', ['null' => true]);

            $this->entityCollection
                ->expects($this->any())
                ->method('getItems')
                ->willReturn($collectionItems);

            if ($collectionItems) {
                /** @var \Magento\Sales\Model\AbstractModel|\PHPUnit_Framework_MockObject_MockObject $collectionItem */
                $collectionItem = $collectionItems[0];

                $this->emailSender
                    ->expects($this->once())
                    ->method('send')
                    ->with($collectionItem, true)
                    ->willReturn($emailSendingResult);

                if ($emailSendingResult) {
                    $collectionItem
                        ->expects($this->once())
                        ->method('setEmailSent')
                        ->with(true)
                        ->willReturn($collectionItem);

                    $this->entityResource
                        ->expects($this->once())
                        ->method('save')
                        ->with($collectionItem);
                }
            }
        }

        $this->object->sendEmails();
    }

    /**
     * @return array
     */
    public function executeDataProvider()
    {
        $entityModel = $this->getMockForAbstractClass(
            'Magento\Sales\Model\AbstractModel',
            [],
            '',
            false,
            false,
            true,
            ['setEmailSent']
        );

        return [
            [1, [$entityModel], true],
            [1, [$entityModel], false],
            [1, [], null],
            [0, null, null]
        ];
    }
}
