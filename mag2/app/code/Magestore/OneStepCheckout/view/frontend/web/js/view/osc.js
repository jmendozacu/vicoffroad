/*
 * *
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *  
 */
define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'mage/translate',
        'Magento_Checkout/js/model/quote',
        'Magestore_OneStepCheckout/js/action/validate-shipping-info',
        'Magestore_OneStepCheckout/js/action/showLoader',
        'Magestore_OneStepCheckout/js/action/save-shipping-address',
        'Magestore_OneStepCheckout/js/action/set-shipping-information',
        'Magestore_OneStepCheckout/js/model/shipping-rate-service',
        'Magestore_OneStepCheckout/js/action/save-additional-information',
        'Magento_Ui/js/modal/alert',
        'Magestore_OneStepCheckout/js/view/gift-message'
    ],
    function (
        $,
        Component,
        ko,
        $t,
        quote,
        ValidateShippingInfo,
        Loader,
        SaveAddressBeforePlaceOrder,
        setShippingInformationAction,
        shippingRateService,
        saveAdditionalInformation,
        alertPopup,
        giftMessageView
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magestore_OneStepCheckout/onestepcheckout'
            },
            errorMessage: ko.observable(),
            isVirtual:quote.isVirtual,
            enableCheckout: ko.pureComputed(function(){
                return (Loader().loading())?false:true;
            }),
            placingOrder: ko.observable(false),
            initialize: function () {
                this._super();
            },
            prepareToPlaceOrder: function(){
                var self = this;
                if (!quote.paymentMethod()) {
                    alertPopup({
                        content: $t('Please choose a payment method!'),
                        autoOpen: true,
                        clickableOverlay: true,
                        focus: "",
                        actions: {
                            always: function(){

                            }
                        }
                    });
                }
                if(self.validateInformation() == true){
                    self.placingOrder(true);
                    Loader().all(true);
                    var deferred = saveAdditionalInformation();
                    deferred.done(function () {
                        if ($('#allow_gift_messages').length > 0) {
                            var giftMessageDeferred;
                            if ($('#allow_gift_messages').attr('checked') == 'checked') {
                                giftMessageDeferred = giftMessageView().submitOptions();
                                giftMessageDeferred.done(function () {
                                    self.placeOrder();
                                });
                            } else {
                                giftMessageDeferred = giftMessageView().deleteOptions();
                                giftMessageDeferred.done(function () {
                                    self.placeOrder();
                                });
                            }
                        } else {
                            self.placeOrder();
                        }

                    });

                }else{

                }
            },

            placeOrder: function () {
                var self = this;


                SaveAddressBeforePlaceOrder();
                if(this.isVirtual()){
                    if($("#co-payment-form ._active button[type='submit']").length > 0){
                        $("#co-payment-form ._active button[type='submit']").click();
                        self.placingOrder(false);
                        Loader().all(false);
                    }
                }else{
                    setShippingInformationAction().always(
                        function () {
                            shippingRateService().stop(false);
                            if($("#co-payment-form ._active button[type='submit']").length > 0){
                                $("#co-payment-form ._active button[type='submit']").click();
                                self.placingOrder(false);
                                Loader().all(false);
                            }
                        }
                    );
                }
            },

            validateInformation: function(){
                var shipping = (this.isVirtual())?true:ValidateShippingInfo();
                var billing = this.validateBillingInfo();
                var checkPass = true;
                var checkPassConfirm = true;
                if ($('#create-new-account:checkbox:checked').length > 0) {
                    if($('#password').val() ==''){
                        $('.password-field').addClass('_error');
                        $('.password-field .mage-error').show();
                        $('.password-field .mage-error').text('This is a required field.');
                        checkPass = false;
                    }else if($('#password').val().length < 8){
                        $('.password-field').addClass('_error');
                        $('.password-field .mage-error').show();
                        $('.password-field .mage-error').text('Minimum length of this field must be equal or greater than 8 symbols. Leading and trailing spaces will be ignored.');
                        checkPass = false;
                    }else{
                        $('.password-field').removeClass('_error');
                        $('.password-field .mage-error').hide();
                    }
                    
                    if($('#password_confirmation').val() ==''){
                        $('.password-confirmation-field').addClass('_error');
                        $('.password-confirmation-field .mage-error').show();
                        $('.password-confirmation-field .mage-error').text('This is a required field.');
                        checkPassConfirm = false;
                    }else if($('#password_confirmation').val() != $('#password').val()){
                        $('.password-confirmation-field').addClass('_error');
                        $('.password-confirmation-field .mage-error').show();
                        $('.password-confirmation-field .mage-error').text('Please enter the same value again.');
                        checkPassConfirm = false;
                    }else{
                        $('.password-confirmation-field').removeClass('_error');
                        $('.password-confirmation-field .mage-error').hide();
                    }
                    return checkPass && checkPassConfirm;
                }else{
                    $('.password-field').removeClass('_error');
                        $('.password-field .mage-error').hide();
                        $('.password-confirmation-field').removeClass('_error');
                        $('.password-confirmation-field .mage-error').hide();
                }
                return shipping && billing;
            },
            
            afterRender: function(){
                $('#checkout-loader').removeClass('show');
            },
            
            validateBillingInfo: function(){
                if($("#co-payment-form ._active button[type='submit']").length > 0){
                    if($("#co-payment-form ._active button[type='submit']").hasClass('disabled')){
                        if($("#co-payment-form ._active button.update-address-button").length > 0){
                            this.showErrorMessage($t('Please update your billing address'));
                        }
                        return false;
                    }else{
                        return true;
                    }
                }
                return false;
            },
            showErrorMessage: function(message){
                var self = this;
                var timeout = 5000;
                self.errorMessage($t(message));
                setTimeout(function(){
                    self.errorMessage('');
                },timeout);
            }
        });
    }
);
