<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ImportExport\Test\Unit\Model\Import;

use Magento\ImportExport\Model\Import\Adapter as Adapter;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Adapter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = $this->getMock(
            '\Magento\ImportExport\Model\Import\Adapter',
            [],
            [],
            '',
            false
        );
    }

    public function testFactory()
    {
        $this->markTestSkipped('Skipped because factory method has static modifier');
    }

    public function testFindAdapterFor()
    {
        $this->markTestSkipped('Skipped because findAdapterFor method has static modifier');
    }
}
