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
    'Magestore_OneStepCheckout/js/action/convert-shipping-method-code',
    'Magestore_OneStepCheckout/js/action/save-shipping-method',
    'Magestore_OneStepCheckout/js/model/validators-list',
    'jquery/ui',
    'Magestore_OneStepCheckout/js/ajax-loading'
], function ($, quote, convertShippingMethodCode, saveShippingMethodAction, validatorsList) {
    $.widget("magestore.shippingMethod", $.magestore.ajaxLoading, {
        options: {},

        /**
         * shippingMethod creation
         * @protected
         */
        _create: function () {
            var self = this, options = this.options;
            $.extend(this, {
                shippingMethodSelector: '[name=shipping_method]',
                $shippingMethodAvailable: $('#onestepcheckout-shipping-method-section'),
                overlaySelector: '#control_overlay_shipping',
                loaderSelector: '.ajax-loader-shipping-method'
            });

            this.updateObserver();
            quote.shippingMethod.subscribe(function() {
                saveShippingMethodAction();
            });

            var shippingMethod = convertShippingMethodCode.hashToString(window.checkoutConfig.selectedShippingMethod);
            if(shippingMethod) {
                $(this.shippingMethodSelector + '[value="' + shippingMethod + '"]').prop('checked', true);
            }

            $(this.shippingMethodSelector + ':checked').click();

            validatorsList.registerFormValidator($('#shipping-address-form'));
        },

        updateObserver: function () {
            var self = this, options = this.options;

            $(this.shippingMethodSelector).click(function () {
                quote.shippingMethod(convertShippingMethodCode.stringToHash(self.getShippingMethod()));

                /**
                 * event change shipping method
                 */
                $(self.element).trigger('changeShippingMethod', {
                    shippingMethod: self.getShippingMethod()
                });
            });

        },

        /**
         * Get shipping method
         *
         * @return string
         */
        getShippingMethod: function () {
            var currentRadioShippingMethod = $('#onestepcheckout-shipping-method-section input[name="shipping_method"]:checked');

            return currentRadioShippingMethod.length ? (currentRadioShippingMethod.data('carrier')
            + '|' +  currentRadioShippingMethod.data('carrier-method'))  : '';
        },


        /**
         * Update shipping method
         *
         * @param shippingMethodInfo
         */
        updateShippingMethodInfo: function (shippingMethodInfo) {
            this.$shippingMethodAvailable.html(shippingMethodInfo);
            this.updateObserver();
        },


        updateFirstLoadShippingMethod: function () {
            $(this.shippingMethodSelector + ':checked').click();
        }
    });

    return $.magestore.shippingMethod;

});