/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([], function () {
    'use strict';

    return function ($target, $owner, data) {
        $target.find(data.buttonConfiguration).addClass('disabled')
            .attr('disabled', 'disabled');
    };
});
