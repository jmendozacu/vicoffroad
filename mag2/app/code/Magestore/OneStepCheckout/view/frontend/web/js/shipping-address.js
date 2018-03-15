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
    'Magestore_OneStepCheckout/js/model/custom-checkout-data',
    'Magestore_OneStepCheckout/js/model/validators-list',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magestore_OneStepCheckout/js/action/save-shipping-address',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/address-converter',
    'jquery/ui',
    'mage/mage',
    'Magestore_OneStepCheckout/js/address',
], function (
    $,
    customCheckoutData,
    validatorsList,
    selectShippingAddress,
    selectBillingAddress,
    saveShippingAddressAction,
    quote,
    addressConverter
) {
    $.widget("magestore.shippingAddress", $.magestore.address, {
        options: {
            requireFields: []
        },

        /**
         * shippingAddress creation
         * @protected
         */
        _create: function () {
            this._super();
            var self = this, options = this.options;
            $.extend(this, {
                type: 'shipping',
                requireFields: ['country_id', 'region_id', 'region', 'postcode', 'city'],
                $billingControl: $('[name="billing[different_billing]"]')
            });

            this.$inputEmail.change(function () {
                quote.guestEmail = $(this).val();
            });

            window.addressConverter = addressConverter;
            window.validatorsList = validatorsList;

            validatorsList.registerFormValidator($('#shipping-address-form'));

            self.applyAddress(false);

            this.initObserver();
            saveShippingAddressAction(true);
        },

        /**
         * init event listener
         */
        initObserver: function () {
            var self = this, options = this.options;
            this.initAutoCompleteAddress();

            quote.shippingAddress.subscribe(function(){
                if(self.$billingControl.prop('checked')) {
                    selectBillingAddress(quote.shippingAddress());
                }
            });

            $(this.element).find(options.fieldSelector).change(function () {
                customCheckoutData.isSavedCheckoutData(false);
                selectShippingAddress(addressConverter.formAddressDataToQuoteAddress(self.getAddressData()));
            });

        },
    });

    return $.magestore.shippingAddress;

});