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
        'Magestore_OneStepCheckout/js/model/custom-checkout-data',
        'Magestore_OneStepCheckout/js/model/loading-manager',
        'mage/storage',
    ],
    function ($, _, quote, customCheckoutData, loadingManager, storage) {
        'use strict';

        var updateSectionConfig = window.oneStepCheckoutConfig.updateOnChangePaymentMehtod;

        return function () {
            var serviceUrl = window.oneStepCheckoutConfig.savePaymentMethodUrl,
                payload = {
                    payment_method_data: quote.paymentMethod(),
                },
                $orderReviewContainer = $('#checkout-review-load');

            if(quote.isVirtual()) {
                payload.additional_data = customCheckoutData.getAdditionalData();
            }
            loadingManager.startLoaderShippingMethod(false, true);
            loadingManager.startLoaderPaymentMethod(false, true);
            loadingManager.startLoaderOrderReviewMethod(updateSectionConfig.review, true);
            loadingManager.disablePlaceOrder(true);

            return storage.post(
                serviceUrl, JSON.stringify(payload), false
            ).done(
                function (response) {
                    if (response.review_info) {
                        $orderReviewContainer.orderReview('updateOrderReview', response.review_info);
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
