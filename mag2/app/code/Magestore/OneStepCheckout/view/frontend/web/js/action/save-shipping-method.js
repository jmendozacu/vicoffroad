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
        'jquery',
        "underscore",
        'Magento_Checkout/js/model/quote',
        'Magestore_OneStepCheckout/js/action/convert-shipping-method-code',
        'Magestore_OneStepCheckout/js/model/loading-manager',
        'Magestore_OneStepCheckout/js/action/get-payment-information',
        'mage/storage',
        'mage/url',
        'Magestore_OneStepCheckout/js/model/full-screen-loader',
    ],
    function ($, _, quote, convertShippingMethodCode, loadingManager, getPaymentInformation, storage, url, fullScreenLoader) {
        'use strict';
        var updateSectionConfig = window.oneStepCheckoutConfig.updateOnChangeShippingMehtod;
        return function () {
            var serviceUrl = window.oneStepCheckoutConfig.saveShippingMethodUrl,
                payload = {
                    shipping_method: convertShippingMethodCode.hashToString(quote.shippingMethod())
                },
                $orderReviewContainer = $('#checkout-review-load');

            loadingManager.startLoaderShippingMethod(false, true);
            loadingManager.startLoaderPaymentMethod(updateSectionConfig.payment, true);
            loadingManager.startLoaderOrderReviewMethod(updateSectionConfig.review, true);
            loadingManager.disablePlaceOrder(true);

            return storage.post(
                serviceUrl, JSON.stringify(payload), false
            ).done(
                function (response) {
                    if (response.review_info) {
                        $orderReviewContainer.orderReview('updateOrderReview', response.review_info);
                    }
                    if (response.payment_method) {
                        getPaymentInformation();
                        //$paymentMethodContainer.paymentMethod('updatePaymentInfo', result.payment_method);
                    }
                }
            ).fail(
                function (response) {
                }
            ).always(
                function (response) {
                    loadingManager.stopLoaderAll();
                    loadingManager.disablePlaceOrder(false);
                }
            );
        };
    }
);
