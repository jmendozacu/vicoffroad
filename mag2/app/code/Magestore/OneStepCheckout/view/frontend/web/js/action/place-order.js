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
        "jquery",
        "underscore",
        'Magento_Checkout/js/model/quote',
        'Magestore_OneStepCheckout/js/model/custom-checkout-data',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magestore_OneStepCheckout/js/model/full-screen-loader',
        'Magento_Customer/js/customer-data',
    ],
    function ($,_, quote, customCheckoutData, urlBuilder, storage, url, errorProcessor, customer, fullScreenLoader, customerData) {
        'use strict';

        return function (paymentData, redirectOnSuccess, messageContainer) {
            var serviceUrl,
                payload = {
                    cartId: quote.getQuoteId(),
                    paymentMethod: paymentData,
                    billingAddress: quote.billingAddress()
                };

            redirectOnSuccess = redirectOnSuccess !== false;

            /** Checkout for guest and registered customer. */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:quoteId/payment-information', {
                    quoteId: quote.getQuoteId()
                });
                _.extend(payload, {
                    email: quote.guestEmail,
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
            }

            fullScreenLoader.startLoader();

            return storage.post(
                serviceUrl, JSON.stringify(payload), false
            ).done(
                function (response) {
                    if (redirectOnSuccess) {
                        var miniCart = $('[data-block="minicart"]');
                        miniCart.trigger('contentLoading');
                        customerData.reload('cart', true);
                        miniCart.trigger('contentUpdated');
                        miniCart.on('contentUpdated', function(event) {
                            if (response) {
                                window.location.replace(url.build('checkout/onepage/success/'));
                            } else {
                                window.location.replace(url.build('checkout/cart/'));
                            }
                        });
                    }
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
