/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([], function () {
    'use strict';

    return function ($target, $owner, data) {
        if ($target.find(data.enableButton).val() === '0') {
            $target.find(data.enableExpress).prop('disabled', true);
        }
    };
});
