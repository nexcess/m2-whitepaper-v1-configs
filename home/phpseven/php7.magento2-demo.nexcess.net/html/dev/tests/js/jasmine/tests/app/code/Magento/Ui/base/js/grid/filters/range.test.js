/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/grid/filters/range'
], function (_, Group) {
    'use strict';

    describe('ui/js/grid/filters/range', function () {
        var group;

        beforeEach(function () {
            group = new Group({
                elems: [],
                index: 'index',
                name: 'name',
                indexField: 'id',
                dataScope: 'scope',
                provider: 'provider'
            });
        });

        it('Default state - Select no fields.', function () {
            expect(group.elems()).toEqual([]);
            group.elems.push({id:1}, {id:1});
            expect(group.elems()).not.toEqual([]);
        });
        it('Check for reset elements.', function () {
            var elem = {
                value: false,
                reset: function() {
                    this.value = true;
                }
            };

            group.elems.push(elem);
            expect(group.reset()).toBe(group);
            expect(group.elems.first().value).toBe(true);
        });
        it('Check for clear elements.', function () {
            var elem = {
                value: 'text',
                clear: function() {
                    this.value = '';
                }
            };

            group.elems.push(elem);
            expect(group.clear()).toBe(group);
            expect(group.elems.first().value).toEqual('');
        });
        it('Check if some elements has data.', function () {
            var elem = {
                hasData: function() {
                    return true;
                }
            };

            expect(group.hasData()).toBe(false);
            group.elems.push(elem);
            expect(group.hasData()).toBe(true);
        });
        it('Get preview from child elements.', function () {
            var elem = {
                getPreview: function() {
                    return true;
                }
            };

            expect(group.getPreview()).toEqual([]);
            group.elems.push(elem, elem);
            expect(group.getPreview()).toEqual([true, true]);
        });
    });
});
