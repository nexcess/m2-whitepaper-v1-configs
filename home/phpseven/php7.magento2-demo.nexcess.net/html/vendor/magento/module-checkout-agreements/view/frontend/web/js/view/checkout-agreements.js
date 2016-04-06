/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'Magento_CheckoutAgreements/js/model/agreements-modal'
    ],
    function (ko, $, Component, agreementsModal) {
        'use strict';
        var agreementsConfig = window.checkoutConfig.checkoutAgreements,
            agreementManualMode = 1;

        return Component.extend({
            defaults: {
                template: 'Magento_CheckoutAgreements/checkout/checkout-agreements'
            },
            isVisible: agreementsConfig.isEnabled,
            agreements: agreementsConfig.agreements,
            modalTitle: ko.observable(null),
            modalContent: ko.observable(null),
            modalWindow: null,

            /**
             * Checks if agreement required
             *
             * @param element
             */
            isAgreementRequired: function(element) {
                return element.mode == agreementManualMode;
            },

            /**
             * Show agreement content in modal
             *
             * @param element
             */
            showContent: function (element) {
                this.modalTitle(element.checkboxText);
                this.modalContent(element.content);
                agreementsModal.showModal();
            },

            /**
             * Init modal window for rendered element
             *
             * @param element
             */
            initModal: function(element) {
                agreementsModal.createModal(element);
            }
        });
    }
);
