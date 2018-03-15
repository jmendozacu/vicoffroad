/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        "underscore",
        'mage/mage',
        'jquery/ui',
        'mage/mage',
    ],
    function($, _) {
        'use strict';
        var validators = [];
        return {
            /**
             * Register unique validator
             *
             * @param form
             */
            registerFormValidator: function(form) {
                $(form).mage('magestore/validation', {
                    errorPlacement: function (error, element) {
                        if (element.hasClass('validate-require-one-shipping-method')) {
                            $(error).insertAfter(element.parents('.sp-methods'));
                        }

                        var errorPlacement = element;
                        // logic for date-picker error placement
                        if (element.hasClass('hasDatepicker')) {
                            errorPlacement = element.siblings('img');
                        }
                        // logic for field wrapper
                        var fieldWrapper = element.closest('.addon');
                        if (fieldWrapper.length) {
                            errorPlacement = fieldWrapper.after(error);
                        }
                        //logic for checkboxes/radio
                        if (element.is(':checkbox') || element.is(':radio')) {
                            errorPlacement = element.siblings('label').last();
                        }
                        errorPlacement.after(error);
                    }
                });

                validators.push($(form));
            },

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
                    if (item.is(':visible') && item.valid() == false) {
                        validationResult = false;
                        return false;
                    }
                });
                return validationResult;
            }
        };
    }
);
