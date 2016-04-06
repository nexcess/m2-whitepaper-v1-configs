<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Store\Test\Unit\Model;

class WebsiteTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCanDelete()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $websiteCollection = $this->getMock(
            'Magento\Store\Model\ResourceModel\Website\Collection',
            ['getSize'],
            [],
            '',
            false
        );
        $websiteCollection->expects($this->any())->method('getSize')->will($this->returnValue(2));

        $websiteFactory = $this->getMock(
            'Magento\Store\Model\WebsiteFactory',
            ['create', 'getCollection', '__wakeup'],
            [],
            '',
            false
        );
        $websiteFactory->expects($this->any())->method('create')->will($this->returnValue($websiteFactory));
        $websiteFactory->expects($this->any())->method('getCollection')->will($this->returnValue($websiteCollection));

        /** @var \Magento\Store\Model\Website $websiteModel */
        $websiteModel = $objectManager->getObject(
            'Magento\Store\Model\Website',
            ['websiteFactory' => $websiteFactory]
        );
        $websiteModel->setId(2);
        $this->assertTrue($websiteModel->isCanDelete());
    }
}
