/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define,alert*/
define(
    [
        'jquery',
        '../model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'Magento_Checkout/js/model/totals'
    ],
    function ($, quote, resourceUrlManager, errorProcessor, storage, totals) {
        "use strict";
        return function (callbacks, deferred) {
            deferred = deferred || $.Deferred();
            totals.isLoading(true);
            return storage.get(
                resourceUrlManager.getUrlForCartTotals(quote),
                false
            ).done(
                function (response) {
                    totals.isLoading(false);
                    var proceed = true;
                    $.each(callbacks, function(index, callback) {
                        proceed = proceed && callback();
                    });
                    if (proceed) {
                        quote.setTotals(response);
                        deferred.resolve();
                    }
                }
            ).error(
                function (response) {
                    totals.isLoading(false);
                    deferred.reject();
                    errorProcessor.process(response);
                }
            ).always(
                function () {
                    totals.isLoading(false);
                }
            );

        };
    }
);
