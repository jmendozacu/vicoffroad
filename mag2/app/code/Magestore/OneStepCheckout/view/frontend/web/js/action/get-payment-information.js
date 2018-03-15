/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Magestore_OneStepCheckout/js/model/loading-manager',
        'mage/url'
    ],
    function ($, quote, urlBuilder, storage, errorProcessor, customer, methodConverter, paymentService, loadingManager, url) {
        'use strict';
        return function (deferred, messageContainer) {
            var serviceUrl;

            deferred = deferred || $.Deferred();
            /**
             * Checkout for guest and registered customer.
             */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/payment-information', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
            }

            loadingManager.startLoaderPaymentMethod(true, true);
            loadingManager.disablePlaceOrder(true);

            return storage.get(
                serviceUrl, false
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                    deferred.resolve();
                    var totals = response.totals;
                    if(totals.grand_total == '0'){
                        window.location.href = url.build('checkout/cart/index');
                    }
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                    deferred.reject();

                }
            ).always(
                function (response) {
                    loadingManager.stopLoaderPaymentMethod();
                    loadingManager.disablePlaceOrder(false);
                }
            );
        };
    }
);
