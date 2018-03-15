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

define([
    'jquery',
    'mage/storage',
    'jquery/ui',
    'magestore/oneStepCheckout'
], function ($, storage) {
    $.widget("magestore.applyCoupon", {
        options: {},

        /**
         * orderReview creation
         * @protected
         */

        _create: function () {
            var self = this, options = this.options;
            $.extend(this, {
                $billingAddressContainer: $('#onestepcheckout-billing-section'),
                $shippingAddressContainer: $('#onestepcheckout-shipping-section'),
                $shippingMethodContainer: $('#shipping-method-wrapper'),
                $paymentMethodContainer: $('#onestepcheckout-payment-methods'),
                $orderReviewContainer: $('#checkout-review-load'),
                $placeOrderButton: $('#onestepcheckout-button-place-order'),
                $loaderAll: $('.ajax-loader3'),
                $ajaxReviewOverlay: $('#control_overlay_review'),
                $ajaxShippingOverlay: $('#control_overlay_shipping'),
                $ajaxPaymentOverlay: $('#control_overlay_payment'),
                $couponInput: $('#coupon_code_onestepcheckout')
            });

            this.applyObserver();
        },

        applyObserver: function () {
            var self = this, options = this.options;
            $('.apply-coupon').click(function () {

                if (self.$couponInput.val()) {
                    self.showOverLay();
                    var remove, couponCode;
                    if ($(this).attr('id') == 'add_coupon_code_button') {
                        remove = 0;
                    } else {
                        remove = 1;
                    }
                    var params = {
                        couponCode: $('#coupon_code_onestepcheckout').val(),
                        remove: remove
                    };


                    storage.post(
                        'onestepcheckout/coupon/apply',
                        JSON.stringify(params),
                        false
                    ).done(
                        function (result) {
                            self.hideOverlay();
                            if (result.review_info) {
                                self.$orderReviewContainer.orderReview('updateOrderReview', result.review_info);
                            }

                            if (result.payment_method) {
                                getPaymentInformation();
                            }


                            if (result.shipping_method) {
                                self.$shippingMethodContainer.shippingMethod('updateShippingMethodInfo', result.shipping_method);
                            }
                            
                            if (remove == 0 && result.error == false) {
                                $('#add_coupon_code_button').hide();
                                $('#remove_coupon_code_button').show();

                            }

                            if (remove == 1 && result.cancel == true) {
                                $('#add_coupon_code_button').show();
                                $('#remove_coupon_code_button').hide();
                                $('#coupon_code_onestepcheckout').val('');
                            }
                            if (result.error == true) {
                                alert(result.message);
                            }
                        }
                    ).fail(
                        function (result) {

                        }
                    );
                } else {
                    alert('Please Enter Coupon Code');
                }
            });

        },

        showOverLay: function () {
            var self = this, options = this.options;
            self.$loaderAll.show();
            self.$ajaxReviewOverlay.show();
            self.$ajaxShippingOverlay.show();
            self.$ajaxPaymentOverlay.show();
        },

        hideOverlay: function () {
            var self = this, options = this.options;
            self.$loaderAll.hide();
            self.$ajaxReviewOverlay.hide();
            self.$ajaxShippingOverlay.hide();
            self.$ajaxPaymentOverlay.hide();
        }
    });

    return $.magestore.orderReview;

});