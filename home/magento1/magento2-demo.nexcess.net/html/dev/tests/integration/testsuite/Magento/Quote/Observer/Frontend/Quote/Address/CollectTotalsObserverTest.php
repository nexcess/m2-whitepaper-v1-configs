<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Quote\Observer\Frontend\Quote\Address;

use Magento\TestFramework\Helper\Bootstrap;

class CollectTotalsObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Quote\Observer\Frontend\Quote\Address\CollectTotalsObserver
     */
    protected $model;

    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(
            'Magento\Quote\Observer\Frontend\Quote\Address\CollectTotalsObserver'
        );
    }

    /**
     * @magentoConfigFixture current_store customer/create_account/auto_group_assign 1
     * @magentoConfigFixture current_store customer/create_account/default_group 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/quote.php
     *
     * @covers \Magento\Quote\Observer\Frontend\Quote\Address\CollectTotalsObserver::execute
     */
    public function testChangeQuoteCustomerGroupIdForCustomerWithDisabledAutomaticGroupChange()
    {
        /** @var \Magento\Framework\ObjectManagerInterface $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $objectManager->create('Magento\Customer\Model\Customer');
        $customer->load(1);
        $customer->setDisableAutoGroupChange(1);
        $customer->setGroupId(2);
        $customer->save();

        /** @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository */
        $customerRepository = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
        $customerData = $customerRepository->getById($customer->getId());

        /** @var $quote \Magento\Quote\Model\Quote */
        $quote = $objectManager->create('Magento\Quote\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        $quote->setCustomer($customerData);

        $quoteAddress = $quote->getBillingAddress();
        $shippingAssignment = $this->objectManager->create('Magento\Quote\Model\ShippingAssignment');
        $shipping = $this->objectManager->create('Magento\Quote\Model\Shipping');
        $shipping->setAddress($quoteAddress);
        $shippingAssignment->setShipping($shipping);
        /** @var  \Magento\Quote\Model\Quote\Address\Total $total */
        $total = $this->objectManager->create('Magento\Quote\Model\Quote\Address\Total');

        $eventObserver = $objectManager->create(
            'Magento\Framework\Event\Observer',
            ['data' => [
                'quote' => $quote,
                'shipping_assignment' => $shippingAssignment,
                'total' => $total
        ]
            ]
        );
        $this->model->execute($eventObserver);

        $this->assertEquals(2, $quote->getCustomer()->getGroupId());
    }

    /**
     * @magentoConfigFixture current_store customer/create_account/auto_group_assign 1
     * @magentoConfigFixture current_store customer/create_account/default_group 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/quote.php
     *
     * @covers \Magento\Quote\Observer\Frontend\Quote\Address\CollectTotalsObserver::execute
     */
    public function testChangeQuoteCustomerGroupIdForCustomerWithEnabledAutomaticGroupChange()
    {
        /** @var \Magento\Framework\ObjectManagerInterface $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $objectManager->create('Magento\Customer\Model\Customer');
        $customer->load(1);
        $customer->setDisableAutoGroupChange(0);
        $customer->setGroupId(2);
        $customer->save();

        /** @var \Magento\Customer\Model\CustomerRegistry $customerRegistry */
        $customerRegistry = $objectManager->get('Magento\Customer\Model\CustomerRegistry');
        $customerRegistry->remove($customer->getId());

        /** @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository */
        $customerRepository = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
        $customerData = $customerRepository->getById($customer->getId());

        /** @var $quote \Magento\Quote\Model\Quote */
        $quote = $objectManager->create('Magento\Quote\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        $quote->setCustomer($customerData);

        $quoteAddress = $quote->getBillingAddress();

        $shippingAssignment = $this->objectManager->create('Magento\Quote\Model\ShippingAssignment');
        $shipping = $this->objectManager->create('Magento\Quote\Model\Shipping');
        $shipping->setAddress($quoteAddress);
        $shippingAssignment->setShipping($shipping);
        /** @var  \Magento\Quote\Model\Quote\Address\Total $total */
        $total = $this->objectManager->create('Magento\Quote\Model\Quote\Address\Total');

        $eventObserver = $objectManager->create(
            'Magento\Framework\Event\Observer',
            ['data' => [
                'quote' => $quote,
                'shipping_assignment' => $shippingAssignment,
                'total' => $total
            ]
            ]
        );
        $this->model->execute($eventObserver);

        $this->assertEquals(1, $quote->getCustomer()->getGroupId());
    }
}
