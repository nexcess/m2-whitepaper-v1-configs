/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global FORM_KEY*/
define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/backend/tree-suggest',
    'mage/backend/validation'
], function ($) {
    'use strict';

    var clearParentCategory = function () {
        $('#new_category_parent').find('option').each(function () {
            $('#new_category_parent-suggest').treeSuggest('removeOption', null, this);
        });
    };

    $.widget('mage.newCategoryDialog', {
        _create: function () {
            var widget = this;
            $('#new_category_parent').before($('<input>', {
                id: 'new_category_parent-suggest',
                placeholder: $.mage.__('start typing to search category')
            }));

            $('#new_category_parent-suggest').treeSuggest(this.options.suggestOptions)
                .on('suggestbeforeselect', function (event) {
                    clearParentCategory();
                    $(event.target).treeSuggest('close');
                });

            $.validator.addMethod('validate-parent-category', function () {
                return $('#new_category_parent').val() || $('#new_category_parent-suggest').val() === '';
            }, $.mage.__('Choose existing category.'));
            var newCategoryForm = $('#new_category_form');
            newCategoryForm.mage('validation', {
                errorPlacement: function (error, element) {
                    error.insertAfter(element.is('#new_category_parent') ?
                        $('#new_category_parent-suggest').closest('.mage-suggest') :
                        element);
                }
            }).on('highlight.validate', function (e) {
                var options = $(this).validation('option');
                if ($(e.target).is('#new_category_parent')) {
                    options.highlight($('#new_category_parent-suggest').get(0),
                        options.errorClass, options.validClass || '');
                }
            });
            this.element.modal({
                type: 'slide',
                modalClass: 'mage-new-category-dialog form-inline',
                title: $.mage.__('Create Category'),
                opened: function () {
                    var enteredName = $('#category_ids-suggest').val();

                    $('#new_category_name').val(enteredName);
                    if (enteredName === '') {
                        $('#new_category_name').focus();
                    }
                    $('#new_category_messages').html('');
                },
                closed: function () {
                    var validationOptions = newCategoryForm.validation('option');

                    $('#new_category_name, #new_category_parent-suggest').val('');
                    validationOptions.unhighlight($('#new_category_parent-suggest').get(0),
                        validationOptions.errorClass, validationOptions.validClass || '');
                    newCategoryForm.validation('clearError');
                    $('#category_ids-suggest').focus();
                },
                buttons: [{
                    text: $.mage.__('Create Category'),
                    class: 'action-primary',
                    click: function (e) {
                        if (!newCategoryForm.valid()) {
                            return;
                        }
                        var thisButton = $(e.currentTarget);

                        thisButton.prop('disabled', true);

                        var postData = {
                            general: {
                                name: $('#new_category_name').val(),
                                is_active: 1,
                                include_in_menu: 1
                            },
                            parent: $('#new_category_parent').val(),
                            use_config: ['available_sort_by', 'default_sort_by'],
                            form_key: FORM_KEY,
                            return_session_messages_only: 1
                        };

                        var fields = {};

                        $.each($(newCategoryForm).serializeArray(), function(_, field) {
                            if (
                                field.name &&
                                field.name != 'new_category_name' &&
                                field.name != 'new_category_parent'
                            ) {
                                if (fields.hasOwnProperty(field.name)) {
                                    fields[field.name] = $.makeArray(fields[field.name]);
                                    fields[field.name].push(field.value);
                                }
                                else {
                                    fields[field.name] = field.value;
                                }
                            }
                        });
                        $.extend(postData, fields);

                        $.ajax({
                            type: 'POST',
                            url: widget.options.saveCategoryUrl,
                            data: postData,
                            dataType: 'json',
                            context: $('body')
                        }).success(function (data) {
                            if (!data.error) {
                                var $suggest = $('#category_ids-suggest');

                                $suggest.trigger('selectItem', {
                                    id: data.category.entity_id,
                                    label: data.category.name
                                });
                                $('#new_category_name, #new_category_parent-suggest').val('');
                                $suggest.val('');
                                clearParentCategory();
                                $(widget.element).modal('closeModal');
                            } else {
                                $('#new_category_messages').html(data.messages);
                            }
                        }).complete(
                            function () {
                                thisButton.prop('disabled', false);
                            }
                        );
                    }
                }]
            });
        }
    });

    return $.mage.newCategoryDialog;
});
