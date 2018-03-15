/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'Magestore_OneStepCheckout/js/view/payment/iframe',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magestore_OneStepCheckout/js/model/validators-list',
        'Magestore_OneStepCheckout/js/model/custom-checkout-data',
        'Magestore_OneStepCheckout/js/action/set-payment-information',
        'Magestore_OneStepCheckout/js/action/save-shipping-address',
        'Magestore_OneStepCheckout/js/action/save-custom-checkout-data',
        'Magestore_OneStepCheckout/js/model/full-screen-loader'
    ],
    function (
        $,
        Component,
        quote,
        additionalValidators,
        validatorsList,
        customCheckoutData,
        setPaymentInformationAction,
        saveShippingAddressAction,
        saveCustomCheckoutDataAcion,
        fullScreenLoader
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magestore_OneStepCheckout/payment/payflowpro-form'
            },
            placeOrderHandler: null,
            validateHandler: null,

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
                return 'payflowpro';
            },

            isActive: function() {
                return true;
            },

            /**
             * @override
             */
            placeOrder: function () {
                var self = this;

                if (this.validateHandler() && validatorsList.validate()) {
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
        });
    }
);
