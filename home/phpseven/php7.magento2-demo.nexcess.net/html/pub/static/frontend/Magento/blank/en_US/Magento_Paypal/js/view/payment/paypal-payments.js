/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'paypal_express',
                component: 'Magento_Paypal/js/view/payment/method-renderer/paypal-express'
            },
            {
                type: 'paypal_express_bml',
                component: 'Magento_Paypal/js/view/payment/method-renderer/paypal-express-bml'
            },
            {
                type: 'payflow_express',
                component: 'Magento_Paypal/js/view/payment/method-renderer/payflow-express'
            },
            {
                type: 'payflow_express_bml',
                component: 'Magento_Paypal/js/view/payment/method-renderer/payflow-express-bml'
            },
            {
                type: 'payflowpro',
                component: 'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method'
            },
            {
                type: 'payflow_link',
                component: 'Magento_Paypal/js/view/payment/method-renderer/iframe-methods'
            },
            {
                type: 'payflow_advanced',
                component: 'Magento_Paypal/js/view/payment/method-renderer/iframe-methods'
            },
            {
                type: 'hosted_pro',
                component: 'Magento_Paypal/js/view/payment/method-renderer/iframe-methods'
            },
            {
                type: 'paypal_billing_agreement',
                component: 'Magento_Paypal/js/view/payment/method-renderer/paypal-billing-agreement'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
