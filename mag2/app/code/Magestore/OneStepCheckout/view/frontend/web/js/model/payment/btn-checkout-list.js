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
        var btnlist = {
            braintree_paypal: '#braintree-paypal-loggedout .paypal-style-checkout'
        };

        return {
            /**
             * Register unique validator
             *
             * @param form
             */
            registerBtn: function(code) {
                btnlist[code] = '[data-checkout-code="' + code + '"]';
            },

            /**
             *
             * @param code
             * @returns {*}
             */
            getBtn: function (code) {
                  return btnlist[code];
            },

            /**
             *
             * @returns {{}}
             */
            getListBtnSelector: function () {
                return  btnlist;
            },

            /**
             * Register unique validator
             *
             * @param validator
             */
            clickBtn: function(code) {
                $(btnlist[code]).click();
            }
        };
    }
);
