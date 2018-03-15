/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_OneStepCheckout
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

define(
    [
        'mage/storage',
        'Magestore_OneStepCheckout/js/model/full-screen-loader',
        'Magestore_OneStepCheckout/js/action/place-order',
        'Magestore_OneStepCheckout/js/action/save-shipping-address',
        'Magento_Checkout/js/model/error-processor',
    ],
    function (
        storage,
        fullScreenLoader,
        placeOrderAction,
        saveShippingAddressAction,
        errorProcessor
    ) {
        'use strict';

        return function (paymentData, redirectOnSuccess, messageContainer) {
            fullScreenLoader.startLoader();
            return saveShippingAddressAction().done(
                function (response) {
                    placeOrderAction(paymentData, redirectOnSuccess, messageContainer);
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                }
            ).always(function(){
                fullScreenLoader.stopLoader();
            });
        };
    }
);
