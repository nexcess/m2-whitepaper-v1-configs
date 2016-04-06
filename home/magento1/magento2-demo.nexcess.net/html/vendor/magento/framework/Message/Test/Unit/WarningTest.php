<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Message\Test\Unit;

use Magento\Framework\Message\MessageInterface;

/**
 * \Magento\Framework\Message\Warning test case
 */
class WarningTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Message\Warning
     */
    protected $model;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Framework\Message\Warning');
    }

    public function testGetType()
    {
        $this->assertEquals(MessageInterface::TYPE_WARNING, $this->model->getType());
    }
}
