/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/view/messages',
    'Magento_Checkout/js/model/authentication-messages'
], function (Component, messageContainer) {
    'use strict';

    return Component.extend({

        initialize: function (config) {
            return this._super(config, messageContainer);
        }
    });
});
