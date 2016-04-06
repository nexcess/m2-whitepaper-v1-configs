<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Test\Unit\Model\Indexer\Product\Eav\Action;

class FullTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteWithAdapterErrorThrowsException()
    {
        $eavDecimalFactory = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\DecimalFactory',
            ['create'],
            [],
            '',
            false
        );
        $eavSourceFactory = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\SourceFactory',
            ['create'],
            [],
            '',
            false
        );

        $exceptionMessage = 'exception message';
        $exception = new \Exception($exceptionMessage);

        $eavDecimalFactory->expects($this->once())
            ->method('create')
            ->will($this->throwException($exception));

        $model = new \Magento\Catalog\Model\Indexer\Product\Eav\Action\Full(
            $eavDecimalFactory,
            $eavSourceFactory
        );

        $this->setExpectedException('Magento\Framework\Exception\LocalizedException', $exceptionMessage);

        $model->execute();
    }
}
