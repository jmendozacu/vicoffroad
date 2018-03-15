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
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/payment/renderer-list',
    'jquery/ui',
    'Magestore_OneStepCheckout/js/ajax-loading',
], function ($, quote, rendererList) {
    $.widget("magestore.paymentMethod", $.magestore.ajaxLoading, {
        options: {},

        /**
         * paymentMethod creation
         * @protected
         */
        _create: function () {
            var self = this, options = this.options;

            $.extend(this, {
                paymentMethodSelector: '[name="payment[method]"]',
                overlaySelector: '#control_overlay_payment',
                loaderSelector: '.ajax-loader-payment',
                $placeOrderBtn: $('#onestepcheckout-button-place-order'),
                $continueToPayPal: $('#onestepcheckout-button-continue-to-paypal')
            });
            this.updateObserver();
        },

        updateObserver: function () {
            var self = this, options = this.options;
            self.applyPaymentMethod();

            //$(self.paymentMethodSelector).click(function () {
            //    self.applyPaymentMethod();
            //    /**
            //     * event change shipping method
            //     */
            //    $(self.element).trigger('changePaymentMethod', {
            //        paymentMethodData: self.getPaymentMethodData()
            //    });
            //});
        },

        applyPaymentMethod: function () {
            quote.paymentMethod(this.getPaymentMethodData());
            this.showFormPayment();
            switch (quote.paymentMethod().method) {
                case 'paypal_express':
                case 'paypal_express_bml':
                    this.$placeOrderBtn.hide();
                    this.$continueToPayPal.show();
                    break;
                default:
                    this.$placeOrderBtn.show();
                    this.$continueToPayPal.hide();
            }
        },

        /**
         * Show form for current payment method
         */
        showFormPayment: function () {
            var self = this;
            $(self.element).find('.payment-method').hide();
            var $paymentFormContainer = $(self.paymentMethodSelector + ':checked').parents('.radioparent').next('.payment-method');
            $paymentFormContainer.find('[name^="payment["]').change(function () {
                quote.paymentMethod(self.getPaymentMethodData());
            });

            $paymentFormContainer.show();
            $paymentFormContainer.find('[id^=payment_form_]').show();
        },

        /**
         * Get current payment method
         *
         * @return string
         */
        getPaymentMethod: function () {
            var currentRadioPayment = $(this.paymentMethodSelector + ':checked');
            return currentRadioPayment.length ? currentRadioPayment.val() : '';
        },

        /**
         * update payment info
         *
         * @param paymentInfo
         */
        updatePaymentInfo: function (paymentInfo) {
            $(this.element).html(paymentInfo);
            this.updateObserver();
        },

        /**
         * Get payment data
         *
         * @returns {{}|*}
         */
        getPaymentMethodData: function () {
            var $paymentFormContainer = $(this.paymentMethodSelector + ':checked').parents('.radioparent').next('.payment-method'),
                paymentMethodData = {};

            $paymentFormContainer.find('[name^="payment["]').each(function () {
                var fieldName = $(this).prop('name').replace('payment[', '').replace(']', '');
                paymentMethodData[fieldName] = $(this).val();
            });

            paymentMethodData['method'] = this.getPaymentMethod();

            return paymentMethodData;
        },
    });


    return $.magestore.paymentMethod;

});