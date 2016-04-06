<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminNotification\Model\ResourceModel\Inbox\Collection;

class CriticalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection\Critical
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\AdminNotification\Model\ResourceModel\Inbox\Collection\Critical'
        );
    }

    /**
     * @magentoDataFixture Magento/AdminNotification/_files/notifications.php
     */
    public function testCollectionContainsLastUnreadCriticalItem()
    {
        $items = array_values($this->_model->getItems());
        $this->assertEquals('Unread Critical 3', $items[0]->getTitle());
    }
}
