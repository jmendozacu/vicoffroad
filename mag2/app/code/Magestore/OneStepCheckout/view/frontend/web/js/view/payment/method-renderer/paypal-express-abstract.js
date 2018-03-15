/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magestore_OneStepCheckout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magestore_OneStepCheckout/js/model/validators-list',
        'Magestore_OneStepCheckout/js/model/custom-checkout-data',
        'Magestore_OneStepCheckout/js/action/set-payment-method',
        'Magestore_OneStepCheckout/js/action/save-shipping-address',
        'Magestore_OneStepCheckout/js/action/save-custom-checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magestore_OneStepCheckout/js/model/full-screen-loader'
    ],
    function (
        $,
        Component,
        quote,
        validatorsList,
        customCheckoutData,
        setPaymentMethodAction,
        saveShippingAddressAction,
        saveCustomCheckoutDataAcion,
        additionalValidators,
        fullScreenLoader
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magestore_OneStepCheckout/payment/paypal-express-bml',
                billingAgreement: ''
            },

            /** Init observable variables */
            initObservable: function () {
                this._super()
                    .observe('billingAgreement');
                return this;
            },

            /** Open window with  */
            showAcceptanceWindow: function(data, event) {
                window.open(
                    $(event.target).attr('href'),
                    'olcwhatispaypal',
                    'toolbar=no, location=no,' +
                    ' directories=no, status=no,' +
                    ' menubar=no, scrollbars=yes,' +
                    ' resizable=yes, ,left=0,' +
                    ' top=0, width=400, height=350'
                );
                return false;
            },

            /** Returns payment acceptance mark link path */
            getPaymentAcceptanceMarkHref: function() {
                return window.checkoutConfig.payment.paypalExpress.paymentAcceptanceMarkHref;
            },

            /** Returns payment acceptance mark image path */
            getPaymentAcceptanceMarkSrc: function() {
                return window.checkoutConfig.payment.paypalExpress.paymentAcceptanceMarkSrc;
            },

            /** Returns billing agreement data */
            getBillingAgreementCode: function() {
                return window.checkoutConfig.payment.paypalExpress.billingAgreementCode[this.item.method];
            },

            /** Returns payment information data */
            getData: function() {
                var parent = this._super(),
                    additionalData = null;

                if (this.getBillingAgreementCode()) {
                    additionalData = {};
                    additionalData[this.getBillingAgreementCode()] = this.billingAgreement();
                }
                return $.extend(true, parent, {'additional_data': additionalData});
            },

            /** Redirect to paypal */
            continueToPayPal: function () {
                var self = this, action;
                if (this.validate() && validatorsList.validate()) {
                    //update payment method information if additional data was changed
                    this.selectPaymentMethod();

                    fullScreenLoader.startLoader();
                    if(!customCheckoutData.isSavedCheckoutData()) {
                        if(quote.isVirtual()) {
                            action = saveCustomCheckoutDataAcion();
                        } else {
                            action = saveShippingAddressAction(false);
                        }

                        $.when(action).done(function(){
                            setPaymentMethodAction(self.messageContainer);
                        }).always(function(){
                            fullScreenLoader.stopLoader();
                        });
                    } else {
                        setPaymentMethodAction(this.messageContainer);
                    }

                    return false;
                }
            }
        });
    }
);
