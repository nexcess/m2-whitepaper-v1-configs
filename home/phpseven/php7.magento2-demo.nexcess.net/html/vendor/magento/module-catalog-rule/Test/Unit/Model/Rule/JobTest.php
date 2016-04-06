<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogRule\Test\Unit\Model\Rule;

use \Magento\CatalogRule\Model\Rule\Job;

class JobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for method applyAll
     *
     * Checks that invalidate Rule indexer
     *
     * @return void
     */
    public function testApplyAll()
    {
        $ruleProcessorMock = $this->getMock(
            'Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor',
            ['markIndexerAsInvalid'],
            [],
            '',
            false
        );
        $ruleProcessorMock->expects($this->once())->method('markIndexerAsInvalid');
        $jobModel = new Job($ruleProcessorMock);
        $jobModel->applyAll();
    }

    /**
     * @return void
     */
    public function testExceptionApplyAll()
    {
        $ruleProcessorMock = $this->getMock(
            'Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor',
            ['markIndexerAsInvalid'],
            [],
            '',
            false
        );
        $exceptionMessage = 'Test exception message';
        $exceptionCallback = function () use ($exceptionMessage) {
            throw new \Magento\Framework\Exception\LocalizedException(__($exceptionMessage));
        };
        $ruleProcessorMock->expects($this->once())
            ->method('markIndexerAsInvalid')
            ->will($this->returnCallback($exceptionCallback));
        $jobModel = new Job($ruleProcessorMock);
        $jobModel->applyAll();
        $this->assertEquals($exceptionMessage, $jobModel->getError());
    }
}
