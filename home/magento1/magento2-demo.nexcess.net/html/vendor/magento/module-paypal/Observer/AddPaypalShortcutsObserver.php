<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Paypal\Model\Config as PaypalConfig;

/**
 * PayPal module observer
 */
class AddPaypalShortcutsObserver implements ObserverInterface
{
    /**
     * @var \Magento\Paypal\Helper\Shortcut\Factory
     */
    protected $shortcutFactory;

    /**
     * @var PaypalConfig
     */
    protected $paypalConfig;

    /**
     * Constructor
     *
     * @param \Magento\Paypal\Helper\Shortcut\Factory $shortcutFactory
     * @param PaypalConfig $paypalConfig
     */
    public function __construct(
        \Magento\Paypal\Helper\Shortcut\Factory $shortcutFactory,
        PaypalConfig $paypalConfig
    ) {
        $this->shortcutFactory = $shortcutFactory;
        $this->paypalConfig = $paypalConfig;
    }

    /**
     * Add PayPal shortcut buttons
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Catalog\Block\ShortcutButtons $shortcutButtons */
        $shortcutButtons = $observer->getEvent()->getContainer();
        $blocks = [
            'Magento\Paypal\Block\Express\Shortcut' => PaypalConfig::METHOD_WPP_EXPRESS,
            'Magento\Paypal\Block\WpsExpress\Shortcut' => PaypalConfig::METHOD_WPS_EXPRESS,
            'Magento\Paypal\Block\PayflowExpress\Shortcut' => PaypalConfig::METHOD_WPP_PE_EXPRESS,
            'Magento\Paypal\Block\Bml\Shortcut' => PaypalConfig::METHOD_WPP_EXPRESS,
            'Magento\Paypal\Block\WpsBml\Shortcut' => PaypalConfig::METHOD_WPS_EXPRESS,
            'Magento\Paypal\Block\Payflow\Bml\Shortcut' => PaypalConfig::METHOD_WPP_PE_EXPRESS
        ];
        foreach ($blocks as $blockInstanceName => $paymentMethodCode) {
            if (!$this->paypalConfig->isMethodAvailable($paymentMethodCode)) {
                continue;
            }

            $params = [
                'shortcutValidator' => $this->shortcutFactory->create($observer->getEvent()->getCheckoutSession()),
            ];
            if (!in_array('Bml', explode('\\', $blockInstanceName))) {
                $params['checkoutSession'] = $observer->getEvent()->getCheckoutSession();
            }

            // we believe it's \Magento\Framework\View\Element\Template
            $shortcut = $shortcutButtons->getLayout()->createBlock(
                $blockInstanceName,
                '',
                $params
            );
            $shortcut->setIsInCatalogProduct(
                $observer->getEvent()->getIsCatalogProduct()
            )->setShowOrPosition(
                $observer->getEvent()->getOrPosition()
            );
            $shortcutButtons->addShortcut($shortcut);
        }
    }
}
