<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\NewRelicReporting\Test\Unit\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\NewRelicReporting\Model\Observer\ReportProductSavedToNewRelic;

/**
 * Class ReportProductSavedToNewRelicTest
 */
class ReportProductSavedToNewRelicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReportProductSavedToNewRelic
     */
    protected $model;

    /**
     * @var \Magento\NewRelicReporting\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var \Magento\NewRelicReporting\Model\NewRelicWrapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $newRelicWrapper;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->config = $this->getMockBuilder('Magento\NewRelicReporting\Model\Config')
            ->disableOriginalConstructor()
            ->setMethods(['isNewRelicEnabled'])
            ->getMock();
        $this->newRelicWrapper = $this->getMockBuilder('Magento\NewRelicReporting\Model\NewRelicWrapper')
            ->disableOriginalConstructor()
            ->setMethods(['addCustomParameter'])
            ->getMock();

        $this->model = new ReportProductSavedToNewRelic(
            $this->config,
            $this->newRelicWrapper
        );
    }

    /**
     * Test case when module is disabled in config
     *
     * @return void
     */
    public function testReportProductSavedToNewRelicModuleDisabledFromConfig()
    {
        /** @var Observer|\PHPUnit_Framework_MockObject_MockObject $eventObserver */
        $eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->config->expects($this->once())
            ->method('isNewRelicEnabled')
            ->willReturn(false);

        $this->model->execute($eventObserver);
    }

    /**
     * Test case when module is enabled in config
     *
     * @return void
     */
    public function testReportProductSavedToNewRelic()
    {
        /** @var Observer|\PHPUnit_Framework_MockObject_MockObject $eventObserver */
        $eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->config->expects($this->once())
            ->method('isNewRelicEnabled')
            ->willReturn(true);
        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getProduct'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventObserver->expects($this->once())
            ->method('getEvent')
            ->willReturn($event);
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);
        $this->newRelicWrapper->expects($this->once())
            ->method('addCustomParameter')
            ->willReturn(true);

        $this->model->execute($eventObserver);
    }

    /**
     * Test case when module is enabled in config and product updating
     *
     * @dataProvider actionDataProvider
     * @return void
     */
    public function testReportProductUpdatedToNewRelic($isNewObject)
    {
        /** @var Observer|\PHPUnit_Framework_MockObject_MockObject $eventObserver */
        $eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->config->expects($this->once())
            ->method('isNewRelicEnabled')
            ->willReturn(true);
        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getProduct'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventObserver->expects($this->once())
            ->method('getEvent')
            ->willReturn($event);
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->any())
            ->method('isObjectNew')
            ->willReturn($isNewObject);
        $event->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);
        $this->newRelicWrapper->expects($this->once())
            ->method('addCustomParameter')
            ->willReturn(true);

        $this->model->execute($eventObserver);
    }

    public function actionDataProvider()
    {
        return [[true], [false]];
    }
}
