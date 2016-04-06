/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [],
    function() {
        'use strict';
        var validators = [];
        return {
            /**
             * Register unique validator
             *
             * @param validator
             */
            registerValidator: function(validator) {
                validators.push(validator);
            },

            /**
             * Returns array of registered validators
             *
             * @returns {Array}
             */
            getValidators: function() {
                return validators;
            },

            /**
             * Process validators
             *
             * @returns {boolean}
             */
            validate: function() {
                var validationResult = true;

                if (validators.length <= 0) {
                    return validationResult;
                }

                validators.forEach(function(item) {
                    if (item.validate() == false) {
                        validationResult = false;
                        return false;
                    }
                });
                return validationResult;
            }
        };
    }
);
