/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magestore_OneStepCheckout/js/view/payment/default',
    ],
    function (Component, btnCheckoutList) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magestore_OneStepCheckout/payment/checkmo'
            },

            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            /** Returns payable to info */
            getPayableTo: function() {
                return window.checkoutConfig.payment.checkmo.payableTo;
            },
        });
    }
);
