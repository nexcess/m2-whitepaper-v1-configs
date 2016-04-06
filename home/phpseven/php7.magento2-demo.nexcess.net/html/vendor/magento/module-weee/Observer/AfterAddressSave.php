<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Weee\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\Manager;
use Magento\PageCache\Model\Config;
use Magento\Weee\Helper\Data;

class AfterAddressSave implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $weeeHelper;

    /**
     * Module manager
     *
     * @var Manager
     */
    private $moduleManager;

    /**
     * Cache config
     *
     * @var Config
     */
    private $cacheConfig;

    /**
     * @param Session $customerSession
     * @param Data $weeeHelper
     * @param Manager $moduleManager
     * @param Config $cacheConfig
     */
    public function __construct(
        Session $customerSession,
        Data $weeeHelper,
        Manager $moduleManager,
        Config $cacheConfig
    ) {
        $this->customerSession = $customerSession;
        $this->weeeHelper = $weeeHelper;
        $this->moduleManager = $moduleManager;
        $this->cacheConfig = $cacheConfig;
    }

    /**
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleManager->isEnabled('Magento_PageCache') && $this->cacheConfig->isEnabled() &&
            $this->weeeHelper->isEnabled()) {
            /** @var $customerAddress Address */
            $address = $observer->getCustomerAddress();

            // Check if the address is either the default billing, shipping, or both
            if ($address->getIsPrimaryBilling() || $address->getIsDefaultBilling()) {
                $this->customerSession->setDefaultTaxBillingAddress(
                    [
                        'country_id' => $address->getCountryId(),
                        'region_id'  => $address->getRegion() ? $address->getRegionId() : null,
                        'postcode'   => $address->getPostcode(),
                    ]
                );
            }

            if ($address->getIsPrimaryShipping() || $address->getIsDefaultShipping()) {
                $this->customerSession->setDefaultTaxShippingAddress(
                    [
                        'country_id' => $address->getCountryId(),
                        'region_id'  => $address->getRegion() ? $address->getRegionId() : null,
                        'postcode'   => $address->getPostcode(),
                    ]
                );
            }
        }
    }
}
