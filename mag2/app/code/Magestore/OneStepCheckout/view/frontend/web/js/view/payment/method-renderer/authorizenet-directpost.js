/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magestore_OneStepCheckout/js/model/validators-list',
        'Magestore_OneStepCheckout/js/model/custom-checkout-data',
        'Magestore_OneStepCheckout/js/view/payment/iframe',
        'Magestore_OneStepCheckout/js/action/set-payment-information',
        'Magestore_OneStepCheckout/js/action/save-shipping-address',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magestore_OneStepCheckout/js/model/full-screen-loader'
    ],
    function (
        $,
        quote,
        validatorsList,
        customCheckoutData,
        Component,
        setPaymentInformationAction,
        saveShippingAddressAction,
        saveCustomCheckoutDataAcion,
        additionalValidators,
        fullScreenLoader
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magestore_OneStepCheckout/payment/authorizenet-directpost'
            },
            placeOrderHandler: null,
            validateHandler: null,
            initialize: function () {
                this._super();
                window.authorizati = this;
            },
            setPlaceOrderHandler: function(handler) {
                this.placeOrderHandler = handler;
            },

            setValidateHandler: function(handler) {
                this.validateHandler = handler;
            },

            context: function() {
                return this;
            },

            isShowLegend: function() {
                return true;
            },

            getCode: function() {
                return 'authorizenet_directpost';
            },

            isActive: function() {
                return true;
            },

            /**
             * @override
             */
            placeOrder: function () {
                var self = this,
                    setPaymentInformation,
                    saveShippingAddress;

                if (this.validate() && this.validateHandler() && validatorsList.validate()) {
                    this.checkSaveDataAndPlaceOrder();
                }
            },

            /**
             * @override
             */
            processPlaceOrder: function () {
                var self = this,
                    setPaymentInformation;
                this.isPlaceOrderActionAllowed(false);
                fullScreenLoader.startLoader();
                setPaymentInformation = setPaymentInformationAction(this.messageContainer, {
                    'method': self.getCode()
                });

                $.when(setPaymentInformation).done(function () {
                    self.placeOrderHandler().fail(function () {
                        fullScreenLoader.stopLoader();
                    });
                }).fail(function () {
                    fullScreenLoader.stopLoader();
                    self.isPlaceOrderActionAllowed(true);
                }).always(function(){
                    fullScreenLoader.stopLoader();
                });

            },

            /**
             * @override
             */
            validate: function () {
                if(!customCheckoutData.isValidEmail()) {
                    notifyCheckEmail.notifyInvalidEmail();
                    return false;
                }

                if (!quote.paymentMethod()) {
                    alert($.mage.__('Please choose a payment method !'));
                    false;
                }

                if (!quote.isVirtual() && !quote.shippingMethod()) {
                    alert($.mage.__('Please choose a shipping method !'));
                    false;
                }

                if(this.$termInput.length && !this.$termInput.val()) {
                    alert($.mage.__('Please agree to term and conditions !'));

                    return false;
                }

                return true;
            },
        });
    }
);
