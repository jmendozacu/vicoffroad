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
    "underscore",
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magestore_OneStepCheckout/js/model/payment/btn-checkout-list',
    'Magestore_OneStepCheckout/js/model/custom-checkout-data',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magestore_OneStepCheckout/js/action/save-shipping-address',
    'Magestore_OneStepCheckout/js/model/loading-manager',
    'Magestore_OneStepCheckout/js/action/place-order',
    'Magestore_OneStepCheckout/js/action/set-payment-method',
    'Magestore_OneStepCheckout/js/action/save-shipping-method',
    'Magestore_OneStepCheckout/js/action/convert-shipping-method-code',
    'Magestore_OneStepCheckout/js/action/get-payment-information',
    'mage/url',
    'Magento_Customer/js/customer-data',
    'jquery/ui',
    'magestore/oneStepCheckout',
    'magestore/billingAddress',
    'magestore/paymentMethod',
    'magestore/shippingAddress',
    'magestore/shippingMethod',
    'magestore/orderReview',
    'mage/mage',
    "mage/translate",
], function ($,
             _,
             storage,
             quote,
             checkoutData,
             btnCheckoutList,
             customCheckoutData,
             customer,
             selectShippingAddress,
             saveShippingAddressAction,
             loadingManager,
             placeOrderAction,
             setPaymentMethod,
             saveShippingMethodAction,
             convertShippingMethodCode,
             getPaymentInformation,
             url,
             customerData) {
    $.widget("magestore.oneStepCheckout", {
        options: {
            oneStepCheckoutConfig: {
                saveAddressUrl: '',
                saveMethodUrl: '',
                editQtyUrl: '',
                deleteItemUrl: '',
                allowAjaxUpdateOnChangeAddress: true,
                addressConfig: {}
            }
        },

        /**
         * oneStepCheckout creation
         * @protected
         */
        _create: function () {
            var self = this, options = this.options;
            this._initResponsive();
            this._initElementContainer();
            window.quote = quote;
            window.checkoutData = checkoutData;
            window.customCheckoutData = customCheckoutData;
        },

        /**
         * Init responsive style
         * @private
         */
        _initResponsive: function () {
            if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
                $('.onestepcheckout-index-index').addClass('iphone');
            }
        },

        /**
         * Init elements
         * @private
         */
        _initElementContainer: function () {
            var self = this, options = this.options;
            //console.log(options);
            $.extend(this, {
                $billingAddressContainer: $('.onestepcheckout-billing-section'),
                $shippingAddressContainer: $('#onestepcheckout-shipping-section'),
                $shippingMethodContainer: $('#shipping-method-wrapper'),
                $paymentMethodContainer: $('#onestepcheckout-payment-methods'),
                $orderReviewContainer: $('#checkout-review-load'),
                $placeOrderButton: $('#onestepcheckout-button-place-order'),
                $loaderAll: $('.ajax-loader3'),
                $giftWrapCheckBox: $('#onestepcheckout_giftwrap_checkbox'),
                $continueToPayPal: $('#onestepcheckout-button-continue-to-paypal'),
                $addtionalDataElement: $('.osc-additional-data'),
            });

            this.$placeOrderButton.click(function () {
                if (quote.paymentMethod()) {
                    btnCheckoutList.clickBtn(quote.paymentMethod().method);
                } else {
                    alert($.mage.__('Please select payment method.'))
                }
            });

            this.$billingAddressContainer.billingAddress(options.oneStepCheckoutConfig.addressConfig);

            if (!quote.isVirtual()) {
                this.$shippingAddressContainer.shippingAddress(options.oneStepCheckoutConfig.addressConfig);
                this.$shippingMethodContainer.shippingMethod({});
            }

            this.$paymentMethodContainer.paymentMethod({});
            this.$orderReviewContainer.orderReview({});

            /**
             * Save address when is the first load
             */
            //saveShippingAddressAction(true);

            /**
             * Observe event change qantity
             */
            this.$orderReviewContainer.on('changeQty', function (event, data) {
                self.changeQty(data.itemId, data.qty);
            });

            /**
             * Observe event delete Item
             */
            this.$orderReviewContainer.on('deleteItem', function (event, data) {
                self.deleteItem(data.itemId);
            });

            this.$addtionalDataElement.change(function () {
                customCheckoutData.isSavedCheckoutData(false);
            });

            this.$giftWrapCheckBox.click(function () {
                var isWrap;
                if (self.$giftWrapCheckBox.prop('checked')) {
                    isWrap = 1;
                } else {
                    isWrap = 0;
                }

                var params = {
                    isWrap: isWrap
                };

                loadingManager.startLoaderAll();
                loadingManager.disablePlaceOrder(true);
                storage.post(
                    'onestepcheckout/giftWrap/process',
                    JSON.stringify(params),
                    false
                ).done(
                    function (result) {
                        if (result.review_info) {
                            self.$orderReviewContainer.orderReview('updateOrderReview', result.review_info);
                        }

                        if (result.shipping_method) {
                            self.$shippingMethodContainer.shippingMethod('updateShippingMethodInfo', result.shipping_method);
                        }

                        if (result.payment_method) {
                            getPaymentInformation();
                        }
                    }
                ).fail(
                    function (result) {
                    }
                ).always(
                    function (response) {
                        loadingManager.stopLoaderAll();
                        loadingManager.disablePlaceOrder(false);
                    }
                );
            });
        },
        reloadMiniCart: function () {
            var miniCart = $('[data-block="minicart"]');
            miniCart.trigger('contentLoading');
            customerData.reload('cart', true);
            miniCart.trigger('contentUpdated');
        },
        /**
         * Change qty for item
         * @param itemId
         * @param qty
         */
        changeQty: function (itemId, qty) {
            var self = this, options = this.options;
            var params = {
                itemId: itemId,
                qty: qty,
            };

            loadingManager.startLoaderAll();
            loadingManager.disablePlaceOrder(true);
            storage.post(
                options.oneStepCheckoutConfig.editQtyUrl,
                JSON.stringify(params),
                false
            ).done(
                function (result) {
                    if (result.is_virtual) {
                        window.location.reload();
                    } else if (result.empty_quote) {
                        self.reloadMiniCart();
                        setTimeout(function() {
                            window.location.href = url.build('checkout/cart/index');
                        }, 7000);
                    } else {
                        if (result.review_info) {
                            self.$orderReviewContainer.orderReview('updateOrderReview', result.review_info);
                        }
                        if (result.shipping_method) {
                            self.$shippingMethodContainer.shippingMethod('updateShippingMethodInfo', result.shipping_method);
                        }
                        if (result.giftWrap_amount) {
                            $('.onestepcheckout-giftwrap .price').html(result.giftWrap_amount);
                        }
                        if (result.payment_method) {
                            getPaymentInformation();
                        }
                        self.reloadMiniCart();
                    }
                }
            ).fail(
                function (result) {
                }
            ).always(
                function (response) {
                    loadingManager.stopLoaderAll();
                    loadingManager.disablePlaceOrder(false);
                }
            );
        },

        /**
         * Delete item by id
         * @param itemId
         */
        deleteItem: function (itemId) {
            var self = this, options = this.options;
            var params = {
                itemId: itemId
            };

            loadingManager.startLoaderAll();
            loadingManager.disablePlaceOrder(true);
            storage.post(
                options.oneStepCheckoutConfig.deleteItemUrl,
                JSON.stringify(params),
                false
            ).done(
                function (result) {
                    if (result.is_virtual) {
                        window.location.reload();
                    } else if (result.empty_quote) {
                        self.reloadMiniCart();
                        setTimeout(function() {
                            window.location.href = url.build('checkout/cart/index');
                        }, 7000);
                    } else {
                        if (result.review_info) {
                            self.$orderReviewContainer.orderReview('updateOrderReview', result.review_info);
                        }
                        if (result.shipping_method) {
                            self.$shippingMethodContainer.shippingMethod('updateShippingMethodInfo', result.shipping_method);
                        }
                        if (result.giftWrap_amount) {
                            $('.onestepcheckout-giftwrap .price').html(result.giftWrap_amount);
                        }
                        if (result.payment_method) {
                            getPaymentInformation();
                        }
                        self.reloadMiniCart();
                    }
                }
            ).fail(
                function (result) {
                }
            ).always(
                function (response) {
                    loadingManager.stopLoaderAll();
                    loadingManager.disablePlaceOrder(false);
                }
            );
        },

        /**
         * Get selected shipping method
         * @returns {*}
         */
        getShippingMethod: function () {
            return this.$shippingMethodContainer.shippingMethod('getShippingMethod')
        },

        /**
         * Get selected payment method
         * @returns {*}
         */
        getPaymentMethod: function () {
            return this.$paymentMethodContainer.paymentMethod('getPaymentMethod')
        },

        /**
         * Get payment data
         *
         * @returns {*}
         */
        getPaymentMethodData: function () {
            return this.$paymentMethodContainer.paymentMethod('getPaymentMethodData')
        },

        /**
         * Get shipping address data
         *
         * @return array
         */
        getShippingAddressData: function () {
            return this.$shippingAddressContainer.shippingAddress('getAddressData');
        },

        /**
         * Get billing address data
         *
         * @return array
         */
        getBillingAddressData: function () {
            return this.$billingAddressContainer.billingAddress('getAddressData');
        },

        /**
         * Get selected shipping address id in address book.
         */
        getSelectedShippingAddressId: function () {
            return this.$shippingAddressContainer.shippingAddress('getSelectedAddressId');
        },

        /**
         * Get selected billing address id in address book.
         */
        getSelectedBillingAddressId: function () {
            return this.$billingAddressContainer.billingAddress('getSelectedAddressId');
        }

    });

    return $.magestore.oneStepCheckout;

});
