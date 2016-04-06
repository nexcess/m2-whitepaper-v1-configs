/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/*eslint max-nested-callbacks: 0*/

define([
    'uiRegistry',
    'Magento_Ui/js/form/element/post-code'
], function (registry, PostCodeElement) {
    'use strict';

    describe('Magento_Ui/js/form/element/post-code', function () {
        var params, model;

        beforeEach(function () {
            params = {
                dataScope: 'post-code'
            };
            model = new PostCodeElement(params);
        });

        describe('update method', function () {
            it('check for default', function () {
                var value = 'Value',
                    country = {
                        indexedOptions: {
                            'Value': {
                                'is_zipcode_optional': true
                            }
                        }
                    };

                spyOn(registry, 'get').and.returnValue(country);
                model.update(value);
                expect(registry.get).toHaveBeenCalled();
                expect(model.error()).toEqual(false);
                expect(model.required()).toEqual(false);
            });
        });
    });
});
