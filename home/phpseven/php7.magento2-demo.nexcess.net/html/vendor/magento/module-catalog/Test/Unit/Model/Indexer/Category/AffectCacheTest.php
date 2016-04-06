<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Catalog\Test\Unit\Model\Indexer\Category;

class AffectCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\AffectCache
     */
    protected $plugin;

    /**
     * @var \Magento\Framework\Indexer\CacheContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Indexer\ActionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     *  Set up
     */
    public function setUp()
    {
        $this->subjectMock = $this->getMockForAbstractClass('Magento\Framework\Indexer\ActionInterface',
            [], '', false, true, true, []);
        $this->contextMock = $this->getMock('Magento\Framework\Indexer\CacheContext',
            [], [], '', false);
        $this->plugin = new \Magento\Catalog\Model\Indexer\Category\AffectCache($this->contextMock);
    }

    /**
     * test beforeExecute
     */
    public function testBeforeExecute()
    {
        $expectedIds = [5, 6, 7];
        $this->contextMock->expects($this->once())
            ->method('registerEntities')
            ->with($this->equalTo(\Magento\Catalog\Model\Category::ENTITY),
                $this->equalTo($expectedIds))
            ->will($this->returnValue($this->contextMock));
        $actualIds = $this->plugin->beforeExecute($this->subjectMock, $expectedIds);
        $this->assertEquals([$expectedIds], $actualIds);
    }
}
