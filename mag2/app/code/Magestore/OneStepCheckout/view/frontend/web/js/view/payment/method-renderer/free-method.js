/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magestore_OneStepCheckout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
    ],
    function (Component, quote) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magestore_OneStepCheckout/payment/free'
            },

            /** Returns is method available */
            isAvailable: function() {
                return quote.totals().grand_total <= 0;
            }
        });
    }
);
