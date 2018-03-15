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
    ['jquery'],
    function ($) {
        'use strict';

        var $shippingMethodLoader = $('.ajax-loader-shipping-method'),
            $shippingMethodOverlay = $('#control_overlay_shipping'),
            $orderReviewLoader = $('.ajax-loader-order-review'),
            $orderReviewOverlay = $('#control_overlay_review'),
            $paymentMethodLoader = $('.ajax-loader-payment'),
            $paymentMethodOverlay = $('#control_overlay_payment'),
            $placeOrderButton = $('#onestepcheckout-button-place-order');


        /**
         * Start shipping method loader action
         */
        function startLoaderShippingMethod (isShowloader, isShowOverlay) {
            if(isShowloader) {
                $shippingMethodLoader.show();
            }

            if(isShowOverlay) {
                $shippingMethodOverlay.show();
            }
        }

        /**
         * Stop shipping method loader action
         */
        function stopLoaderShippingMethod() {
            $shippingMethodLoader.hide();
            $shippingMethodOverlay.hide();
        }

        /**
         * Start order review loader action
         */
        function startLoaderOrderReviewMethod(isShowloader, isShowOverlay) {
            if(isShowloader) {
                $orderReviewLoader.show();
            }

            if(isShowOverlay) {
                $orderReviewOverlay.show();
            }
        }

        /**
         * Stop order review loader action
         */
        function stopLoaderOrderReviewMethod() {
            $orderReviewLoader.hide();
            $orderReviewOverlay.hide();
        }

        /**
         * Stop payment method loader action
         */
        function startLoaderPaymentMethod(isShowloader, isShowOverlay) {
            if(isShowloader) {
                $paymentMethodLoader.show();
            }

            if(isShowOverlay) {
                $paymentMethodOverlay.show();
            }
        }

        /**
         * Stop payment method loader action
         */
        function stopLoaderPaymentMethod() {
            $paymentMethodLoader.hide();
            $paymentMethodOverlay.hide();
        }

        function startLoaderAll() {
            startLoaderShippingMethod(true, true);
            startLoaderOrderReviewMethod(true, true);
            startLoaderPaymentMethod(true, true);
        }

        function stopLoaderAll() {
            stopLoaderShippingMethod();
            stopLoaderOrderReviewMethod();
            stopLoaderPaymentMethod();
        }

        function disablePlaceOrder(flag) {
            $placeOrderButton.prop('disabled', flag);
        }

        return {
            startLoaderShippingMethod: startLoaderShippingMethod,
            stopLoaderShippingMethod: stopLoaderShippingMethod,
            startLoaderOrderReviewMethod: startLoaderOrderReviewMethod,
            stopLoaderOrderReviewMethod: stopLoaderOrderReviewMethod,
            startLoaderPaymentMethod: startLoaderPaymentMethod,
            stopLoaderPaymentMethod: stopLoaderPaymentMethod,
            startLoaderAll: startLoaderAll,
            stopLoaderAll: stopLoaderAll,
            disablePlaceOrder: disablePlaceOrder
        };
    }
);
