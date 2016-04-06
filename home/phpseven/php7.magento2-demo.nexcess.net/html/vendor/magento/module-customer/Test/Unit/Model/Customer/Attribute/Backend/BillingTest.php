<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Model\Customer\Attribute\Backend;

use Magento\Customer\Model\Customer\Attribute\Backend\Billing;

class BillingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Billing
     */
    protected $testable;

    public function setUp()
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();
        /** @var \Psr\Log\LoggerInterface $logger */
        $this->testable = new \Magento\Customer\Model\Customer\Attribute\Backend\Billing($logger);
    }

    public function testBeforeSave()
    {
        $object = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultBilling', 'unsetDefaultBilling'])
            ->getMock();

        $object->expects($this->once())->method('getDefaultBilling')->will($this->returnValue(null));
        $object->expects($this->once())->method('unsetDefaultBilling')->will($this->returnSelf());
        /** @var \Magento\Framework\DataObject $object */

        $this->testable->beforeSave($object);
    }

    public function testAfterSave()
    {
        $addressId = 1;
        $attributeCode = 'attribute_code';
        $defaultBilling = 'default billing address';
        $object = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultBilling', 'getAddresses', 'setDefaultBilling'])
            ->getMock();

        $address = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->setMethods(['getPostIndex', 'getId'])
            ->getMock();

        $attribute = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
            ->setMethods(['__wakeup', 'getEntity', 'getAttributeCode'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $entity = $this->getMockBuilder('Magento\Eav\Model\Entity\AbstractEntity')
            ->setMethods(['saveAttribute'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $attribute->expects($this->once())->method('getEntity')->will($this->returnValue($entity));
        $attribute->expects($this->once())->method('getAttributeCode')->will($this->returnValue($attributeCode));
        $entity->expects($this->once())->method('saveAttribute')->with($this->logicalOr($object, $attributeCode));
        $address->expects($this->once())->method('getPostIndex')->will($this->returnValue($defaultBilling));
        $address->expects($this->once())->method('getId')->will($this->returnValue($addressId));
        $object->expects($this->once())->method('getDefaultBilling')->will($this->returnValue($defaultBilling));
        $object->expects($this->once())->method('setDefaultBilling')->with($addressId)->will($this->returnSelf());
        $object->expects($this->once())->method('getAddresses')->will($this->returnValue([$address]));
        /** @var \Magento\Framework\DataObject $object */
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */

        $this->testable->setAttribute($attribute);
        $this->testable->afterSave($object);
    }
}
