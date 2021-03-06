/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magestore_OneStepCheckout/js/view/payment/method-renderer/paypal-express-abstract'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magestore_OneStepCheckout/payment/paypal-express-bml'
            }
        });
    }
);
