/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        "underscore",
        'uiComponent',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/checkout-data-resolver'
    ],
    function (
        $,
        _,
        Component,
        ko,
        quote,
        stepNavigator,
        paymentService,
        methodConverter,
        getPaymentInformation,
        checkoutDataResolver
    ) {
        'use strict';

        /** Set payment methods to collection */
        paymentService.setPaymentMethods(methodConverter(window.checkoutConfig.paymentMethods));

        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/payment',
                activeMethod: ''
            },
            isVisible: ko.observable(quote.isVirtual()),
            quoteIsVirtual: quote.isVirtual(),
            isPaymentMethodsAvailable: ko.computed(function () {
                return paymentService.getAvailablePaymentMethods().length > 0;
            }),

            initialize: function () {
                this._super();
                checkoutDataResolver.resolvePaymentMethod();
                stepNavigator.registerStep(
                    'payment',
                    null,
                    'Review & Payments',
                    this.isVisible,
                    _.bind(this.navigate, this),
                    20
                );
                return this;
            },

            navigate: function () {
                var self = this;
                getPaymentInformation().done(function () {
                    self.isVisible(true);
                });
            },

            getFormKey: function() {
                return window.checkoutConfig.formKey;
            }
        });
    }
);
