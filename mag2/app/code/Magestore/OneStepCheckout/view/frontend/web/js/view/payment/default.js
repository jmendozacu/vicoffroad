/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'Magestore_OneStepCheckout/js/action/place-order',
        'Magestore_OneStepCheckout/js/action/save-shipping-address',
        'Magestore_OneStepCheckout/js/action/save-custom-checkout-data',
        'Magento_Checkout/js/action/select-payment-method',
        'Magestore_OneStepCheckout/js/action/save-payment-method',
        'Magestore_OneStepCheckout/js/model/validators-list',
        'Magestore_OneStepCheckout/js/model/payment/btn-checkout-list',
        'Magestore_OneStepCheckout/js/model/custom-checkout-data',
        'Magestore_OneStepCheckout/js/model/notify-check-email',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'uiRegistry',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messages',
        'uiLayout',
        'Magestore_OneStepCheckout/js/model/full-screen-loader'
    ],
    function (
        ko,
        $,
        Component,
        placeOrderAction,
        saveShippingAddressAction,
        saveCustomCheckoutDataAcion,
        selectPaymentMethodAction,
        savePaymentMethodAction,
        validatorsList,
        btnCheckoutList,
        customCheckoutData,
        notifyCheckEmail,
        quote,
        customer,
        paymentService,
        checkoutData,
        checkoutDataResolver,
        registry,
        additionalValidators,
        Messages,
        layout,
        fullScreenLoader
    ) {
        'use strict';
        return Component.extend({
            redirectAfterPlaceOrder: true,
            isVisibleButton: ko.observable(false),
            $termInput: $('#terms_conditions_checkbox_id'),
            /**
             * After place order callback
             */
            afterPlaceOrder: function () {
                //
            },
            isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null),
            /**
             * Initialize view.
             *
             * @returns {Component} Chainable.
             */
            initialize: function () {
                this._super().initChildren();
                quote.billingAddress.subscribe(function(address) {
                    this.isPlaceOrderActionAllowed((address !== null));
                }, this);
                checkoutDataResolver.resolveBillingAddress();

                var billingAddressCode = 'billingAddress' + this.getCode();
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var defaultAddressData = checkoutProvider.get(billingAddressCode);
                    if (defaultAddressData === undefined) {
                        // skip if payment does not have a billing address form
                        return;
                    }
                    var billingAddressData = checkoutData.getBillingAddressFromData();
                    if (billingAddressData) {
                        checkoutProvider.set(
                            billingAddressCode,
                            $.extend({}, defaultAddressData, billingAddressData)
                        );
                    }
                    checkoutProvider.on(billingAddressCode, function (billingAddressData) {
                        checkoutData.setBillingAddressFromData(billingAddressData);
                    }, billingAddressCode);
                });

                window.btnCheckoutList = btnCheckoutList;

                btnCheckoutList.registerBtn(this.getCode());

                return this;
            },

            /**
             * Initialize child elements
             *
             * @returns {Component} Chainable.
             */
            initChildren: function () {
                this.messageContainer = new Messages();
                this.createMessagesComponent();

                return this;
            },

            /**
             * Create child message renderer component
             *
             * @returns {Component} Chainable.
             */
            createMessagesComponent: function () {

                var messagesComponent = {
                    parent: this.name,
                    name: this.name + '.messages',
                    displayArea: 'messages',
                    component: 'Magento_Ui/js/view/messages',
                    config: {
                        messageContainer: this.messageContainer
                    }
                };

                layout([messagesComponent]);

                return this;
            },

            /**
             * Place order.
             */
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && validatorsList.validate()) {
                    console.log(this.validate());
                    this.checkSaveDataAndPlaceOrder();
                    return true;
                }

                return false;
            },

            checkSaveDataAndPlaceOrder: function() {
                var self = this, action;
                fullScreenLoader.startLoader();
                if(!customCheckoutData.isSavedCheckoutData()) {
                    if(quote.isVirtual()) {
                        action = saveCustomCheckoutDataAcion()
                    } else {
                        action = saveShippingAddressAction(false);
                    }

                    $.when(action).done(function(){
                        self.processPlaceOrder();
                    });
                } else {
                    self.processPlaceOrder();
                }
            },

            /**
             * process place order
             */
            processPlaceOrder: function () {
                var self = this,
                    placeOrder;

                this.isPlaceOrderActionAllowed(false);
                placeOrder = placeOrderAction(this.getData(), this.redirectAfterPlaceOrder, this.messageContainer);
                $.when(placeOrder).fail(function () {
                    self.isPlaceOrderActionAllowed(true);
                }).done(this.afterPlaceOrder.bind(this));
            },

            selectPaymentMethod: function() {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);

                savePaymentMethodAction();
                return true;
            },

            isChecked: ko.computed(function () {
                return quote.paymentMethod() ? quote.paymentMethod().method : window.oneStepCheckoutConfig.defaultPaymentMethodCode;
            }),

            isRadioButtonVisible: ko.computed(function () {
                return paymentService.getAvailablePaymentMethods().length !== 1;
            }),

            /**
             * Get payment method data
             */
            getData: function() {
                return {
                    "method": this.item.method,
                    "po_number": null,
                    "additional_data": null
                };
            },

            /**
             * Get payment method type.
             */
            getTitle: function () {
                return this.item.title;
            },

            /**
             * Get payment method code.
             */
            getCode: function () {
                return this.item.method;
            },

            validate: function () {
                if(!customCheckoutData.isValidEmail()) {
                    notifyCheckEmail.notifyInvalidEmail();
                    return false;
                }
                if (!quote.paymentMethod()) {
                    alert($.mage.__('Please choose a payment method !'));
                    return false;
                }

                if (!quote.isVirtual() && !quote.shippingMethod()) {
                    alert($.mage.__('Please choose a shipping method !'));

                    return false;
                }

                if(this.$termInput.length && !this.$termInput.prop('checked')) {
                    alert($.mage.__('Please agree to term and conditions !'));

                    return false;
                }

                return true;
            },

            getBillingAddressFormName: function() {
                return 'billing-address-form-' + this.item.method;
            },

            disposeSubscriptions: function () {
                // dispose all active subscriptions
                var billingAddressCode = 'billingAddress' + this.getCode();
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    checkoutProvider.off(billingAddressCode);
                });
            }
        });
    }
);
