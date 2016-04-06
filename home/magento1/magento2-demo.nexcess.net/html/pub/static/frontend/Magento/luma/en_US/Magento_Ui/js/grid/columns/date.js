/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'mageUtils',
    'moment',
    './column'
], function (utils, moment, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            dateFormat: 'MMM D, YYYY h:mm:ss A'
        },

        /**
         * Overrides base method to normalize date format.
         *
         * @returns {DateColumn} Chainable.
         */
        initConfig: function () {
            this._super();

            this.dateFormat = utils.normalizeDate(this.dateFormat);

            return this;
        },

        /**
         * Formats incoming date based on the 'dateFormat' property.
         *
         * @returns {String} Formatted date.
         */
        getLabel: function () {
            var date = moment(this._super());

            date = date.isValid() ?
                date.format(this.dateFormat) :
                '';

            return date;
        }
    });
});
