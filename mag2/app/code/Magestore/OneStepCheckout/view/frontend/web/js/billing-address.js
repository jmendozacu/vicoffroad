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
    'Magestore_OneStepCheckout/js/model/custom-checkout-data',
    'Magestore_OneStepCheckout/js/action/save-shipping-address',
    'Magestore_OneStepCheckout/js/model/validators-list',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/action/select-billing-address',
    'jquery/ui',
    'Magestore_OneStepCheckout/js/address'
], function ($, quote, customCheckoutData, saveShippingAddressAction, validatorsList, addressConverter, selectBillingAddress) {
    var USE_SAME_SHIPPING = 1,
        USE_OTHER_BILLING = 0;
    $.widget("magestore.billingAddress", $.magestore.address, {
        options: {},

        /**
         * billingAddress creation
         * @protected
         */
        _create: function () {
            this._super();
            var self = this, options = this.options;

            $.extend(this, {
                type: 'billing',
                requireFields: ['country_id', 'region_id', 'region', 'postcode', 'city'],
                $billingControl: $('[name="billing[different_billing]"]'),
                $inputBillingControl: $(this.element).find('[name="billing[use_same_shipping]"]'),
            });

            validatorsList.registerFormValidator($('#billing-address-form'));
            this.initObserver();
        },

        initObserver: function () {
            var self = this, options = this.options;
            this.$billingControl.click(function () {
                if ($(this).prop('checked')) {
                    $(self.element).hide();
                    self.$inputBillingControl.val(USE_SAME_SHIPPING);
                    selectBillingAddress(quote.shippingAddress());
                } else {
                    $(self.element).show();
                    self.$inputBillingControl.val(USE_OTHER_BILLING);
                    self.applyAddress(true);
                }
            });

            this.initAutoCompleteAddress();

            if(!this.$billingControl.prop('checked')) {
                self.applyAddress(false);
            }

            $(this.element).find(options.fieldSelector).change(function () {
                if($(this).val()) {
                    $(this).next('.mage-error').remove();
                }
                selectBillingAddress(addressConverter.formAddressDataToQuoteAddress(self.getAddressData()));
            });

            this.$inputEmail.change(function () {
                quote.guestEmail = $(this).val();
            });
        },

        applyAddress: function (saveAddress) {
            var self = this, options = this.options;
            if (self.getSelectedAddressId() && self.customerAddressesJsonData[self.getSelectedAddressId()]) {
                self.hideNewFormAddress();
                selectBillingAddress(addressConverter.formAddressDataToQuoteAddress(self.customerAddressesJsonData[self.getSelectedAddressId()]));
            } else {
                selectBillingAddress(addressConverter.formAddressDataToQuoteAddress(self.getAddressData()));
                self.showNewFormAddress();
            }

            if(saveAddress) {
                saveShippingAddressAction(true);
            }
        },

        /**
         * Check billing is use same shipping or not
         * @returns {boolean}
         */
        useSameShipping: function () {
            return this.$inputBillingControl.val() == 1;
        }
    });

    return $.magestore.billingAddress;

});