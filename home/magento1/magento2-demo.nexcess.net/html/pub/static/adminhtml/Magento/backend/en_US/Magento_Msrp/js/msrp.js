/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'jquery/ui',
    'mage/dropdown',
    'mage/template'
], function ($) {
    'use strict';

    $.widget('mage.addToCart', {
        options: {
            showAddToCart: true,
            submitUrl: '',
            cartButtonId: '',
            singleOpenDropDown: true,
            dialog: {}, // Options for mage/dropdown
            dialogDelay: 500, // Delay in ms after resize dropdown shown again
            origin: '', //Required, type of popup: 'msrp', 'tier' or 'info' popup

            // Selectors
            cartForm: '.form.map.checkout',
            msrpLabelId: '#map-popup-msrp',
            priceLabelId: '#map-popup-price',
            popUpAttr: '[data-role=msrp-popup-template]',
            popupCartButtonId: '#map-popup-button',
            paypalCheckoutButons: '[data-action=checkout-form-submit]',
            popupId: '',
            realPrice: '',
            isSaleable: '',
            msrpPrice: '',
            helpLinkId: '',
            addToCartButton: '',

            // Text options
            productName: '',
            addToCartUrl: ''
        },

        openDropDown: null,
        triggerClass: 'dropdown-active',

        popUpOptions: {
            appendTo: 'body',
            dialogContentClass: 'active',
            closeOnMouseLeave: false,
            autoPosition: true,
            closeOnClickOutside: false,
            'dialogClass': 'popup map-popup-wrapper',
            position: {
                my: 'left top',
                collision: 'fit none',
                at: 'left bottom',
                within: 'body'
            },
            shadowHinter: 'popup popup-pointer'
        },
        popupOpened: false,

        /**
         * Creates widget instance
         * @private
         */
        _create: function () {
            if (this.options.origin === 'msrp') {
                this.initMsrpPopup();
            } else if (this.options.origin === 'info') {
                this.initInfoPopup();
            } else if (this.options.origin === 'tier') {
                this.initTierPopup();
            }
            $(this.options.cartButtonId).on('click', this._addToCartSubmit.bind(this));
        },

        /**
         * Init msrp popup
         * @private
         */
        initMsrpPopup: function () {
            var popupDOM = $(this.options.popUpAttr)[0],
                $msrpPopup = $(popupDOM.innerHTML.trim());

            $msrpPopup.find(this.options.productIdInput).val(this.options.productId);
            $('body').append($msrpPopup);
            $msrpPopup.trigger('contentUpdated');

            $msrpPopup.find('button')
                .on('click',
                this.handleMsrpAddToCart.bind(this))
                .filter(this.options.popupCartButtonId)
                .text($(this.options.addToCartButton).text());

            $msrpPopup.find(this.options.paypalCheckoutButons).on('click',
                this.handleMsrpPaypalCheckout.bind(this));

            $(this.options.popupId).on('click',
                this.openPopup.bind(this));

            this.$popup = $msrpPopup;
        },

        /**
         * Init info popup
         * @private
         */
        initInfoPopup: function () {
            var infoPopupDOM = $('[data-role=msrp-info-template]')[0],
                $infoPopup = $(infoPopupDOM.innerHTML.trim());

            $('body').append($infoPopup);

            $(this.options.helpLinkId).on('click', function (e) {
                this.popUpOptions.position.of = $(e.target);
                $infoPopup.dropdownDialog(this.popUpOptions).dropdownDialog('open');
                this._toggle($infoPopup);
            }.bind(this));

            this.$popup = $infoPopup;
        },

        /**
         * Init tier price popup
         * @private
         */
        initTierPopup: function () {
            var popupDOM = $(this.options.popUpAttr)[0],
                $tierPopup = $(popupDOM.innerHTML.trim());

            $('body').append($tierPopup);
            $tierPopup.find(this.options.productIdInput).val(this.options.productId);
            this.popUpOptions.position.of = $(this.options.helpLinkId);

            $tierPopup.find('button').on('click',
                this.handleTierAddToCart.bind(this))
                .filter(this.options.popupCartButtonId)
                .text($(this.options.addToCartButton).text());

            $tierPopup.find(this.options.paypalCheckoutButons).on('click',
                this.handleTierPaypalCheckout.bind(this));

            $(this.options.attr).on('click', function (e) {
                this.$popup = $tierPopup;
                this.tierOptions = $(e.target).data('tier-price');
                this.openPopup(e);
            }.bind(this));
        },

        /**
         * handle 'AddToCart' click on Msrp popup
         * @param {Object} ev
         *
         * @private
         */
        handleMsrpAddToCart: function (ev) {
            ev.preventDefault();

            if (this.options.addToCartButton) {
                $(this.options.addToCartButton).click();
                this.closePopup(this.$popup);
            }
        },

        /**
         * handle 'paypal checkout buttons' click on Msrp popup
         *
         * @private
         */
        handleMsrpPaypalCheckout: function () {
            this.closePopup(this.$popup);
        },

        /**
         * handle 'AddToCart' click on Tier popup
         *
         * @param {Object} ev
         * @private
         */
        handleTierAddToCart: function (ev) {
            ev.preventDefault();

            if (this.options.addToCartButton &&
                this.options.inputQty && !isNaN(this.tierOptions.qty)
            ) {
                $(this.options.inputQty).val(this.tierOptions.qty);
                $(this.options.addToCartButton).click();
                this.closePopup(this.$popup);
            }
        },

        /**
         * handle 'paypal checkout buttons' click on Tier popup
         *
         * @private
         */
        handleTierPaypalCheckout: function () {
            if (this.options.inputQty && !isNaN(this.tierOptions.qty)
            ) {
                $(this.options.inputQty).val(this.tierOptions.qty);
                this.closePopup(this.$popup);
            }
        },

        /**
         * Open and set up popup
         *
         * @param {Object} event
         */
        openPopup: function (event) {
            var options = this.tierOptions || this.options;

            this.popUpOptions.position.of = $(event.target);
            this.$popup.find(this.options.msrpLabelId).html(options.msrpPrice);
            this.$popup.find(this.options.priceLabelId).html(options.realPrice);
            this.$popup.dropdownDialog(this.popUpOptions).dropdownDialog('open');
            this._toggle(this.$popup);

            if (!this.options.isSaleable) {
                this.$popup.find('form').hide();
            }
        },

        /**
         *
         * @param {HTMLElement} $elem
         * @private
         */
        _toggle: function ($elem) {
            $(document).on('mouseup.msrp touchend.msrp', function (e) {
                if (!$elem.is(e.target) && $elem.has(e.target).length === 0) {
                    this.closePopup($elem);
                }
            }.bind(this));
            $(window).on('resize', function () {
                this.closePopup($elem);
            }.bind(this));
        },

        /**
         *
         * @param {HTMLElement} $elem
         */
        closePopup: function ($elem) {
            $elem.dropdownDialog('close');
            $(document).off('mouseup.msrp touchend.msrp');
        },

        /**
         * Handler for addToCart action
         */
        _addToCartSubmit: function () {
            this.element.trigger('addToCart', this.element);

            if (this.element.data('stop-processing')) {
                return false;
            }

            if (this.options.addToCartButton) {
                $(this.options.addToCartButton).click();

                return false;
            }

            if (this.options.addToCartUrl) {
                $('.mage-dropdown-dialog > .ui-dialog-content').dropdownDialog('close');
            }
            $(this.options.cartForm).submit();

        }
    });

    return $.mage.addToCart;
});
