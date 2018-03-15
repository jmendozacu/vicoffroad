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
    'mage/storage',
    'Magestore_OneStepCheckout/js/model/custom-checkout-data',
    'Magento_Checkout/js/model/quote',
    'Magestore_OneStepCheckout/js/model/notify-check-email',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magestore_OneStepCheckout/js/action/save-shipping-address',
    'Magento_Checkout/js/model/address-converter',
    'mage/translate',
    'jquery/ui',
    'magestore/regionUpdater',
    'magestore/googleApiLoader'
], function ($, storage, customCheckoutData, quote, notifyCheckEmail, selectShippingAddress, saveShippingAddressAction, addressConverter, $t) {
    var RELOAD_ON_ALL_FIELD_ARE_FILL = 1,
        RELOAD_ON_ANY_TRIGGERING_ARE_CHANGE = 2;

    var VALIDATE_EMAIL_VALID = 1,
        VALIDATE_EMAIL_EXISTS = 0,
        VALIDATE_EMAIL_INVALID = -1,
        MESSAGE_INVALID_EMAIL = $t('Invalid Email Address'),
        MESSAGE_EXISTS_EMAIL = $t('Email address already registered. You may <a href="" class="login-link" onclick="return false;">login</a> if you wish to do so'),
        MESSAGE_EXISTS_EMAIL_NO_LOGIN_LINK = $t('Email address already registered. You may login if you wish to do so');

    $.widget("magestore.address", {
        options: {
            regionJson: {},
            requireFields: [],
            reloadSectionType: '1',
            triggeringFieldsChange: [],
            allowAjaxUpdate: true,
            fieldSelector: '[data-field-id]'
        },

        /**
         * shippingAddress creation
         * @protected
         */
        _create: function () {
            var self = this, options = this.options;
            $.extend(this, {
                type: '',
                requireFields: [],
                eventName: 'changeAddress',
                saveAddressBookSelector: '[data-field-id=save_in_address_book]',
                allowAddressFieldName: [
                    'email',
                    'country_id',
                    'region_id',
                    'region_code',
                    'region',
                    'customer_id',
                    'street',
                    'company',
                    'telephone',
                    'fax',
                    'postcode',
                    'city',
                    'firstname',
                    'lastname',
                    'middlename',
                    'prefix',
                    'suffix',
                    'vat_id',
                    'same_as_billing',
                    'customer_address_id',
                    'save_in_address_book'
                ],
                addressFrom: {
                    street_number: 'short_name',
                    route: 'long_name',
                    locality: 'long_name',
                    administrative_area_level_1: 'short_name',
                    country: 'short_name',
                    postal_code: 'short_name',
                    sublocality_level_1: 'long_name'
                },
                $inputEmail: $(this.element).find('[data-field-id="email"]'),
                $notifyEmail: $('#notify-email-invalid'),
                $notifyEmailOverlay: $('#notify-email-invalid-overlay'),
                $validateSuccessIcon: $(this.element).find('.valid-email-address-image'),
                $errorMessage: $(this.element).find('.email-error-message'),
                $inputCheckEmail: $(this.element).find('[name="emailvalid"]'),
                $inputCheckBoxCreateAccount: $(this.element).find('.create-account-checkbox'),
                $passwordSection: $(this.element).find('.password-section'),
                $loginLink: $('#onestepcheckout-login-link'),
                $addressForm: $(this.element).find('.new-address-form'),
                $selectAddress: $(this.element).find('.address-select'),
                customerAddressesJsonData: window.oneStepCheckoutConfig.customerAddressesJsonData
            });

            /**
             * region updater
             */
            $(this.element).find('[name$="[country_id]"]').regionUpdater({
                regionList: $(this.element).find('[name$="[region_id]"]'),
                regionInput: $(this.element).find('[name$="[region]"]'),
                regionJson: options.regionJson,
                defaultRegion: options.defaultRegion,
            });

            /**
             * Password section
             */
            this.$inputCheckBoxCreateAccount.click(function () {
                self.$passwordSection.toggle();
                $(this).val($(this).val() == '0' ? 1 : 0);
            });

            this.$selectAddress.change(function () {
                self.applyAddress(true);
            });

            /**
             * End init observe for select address
             */


            $(this.element).find(this.saveAddressBookSelector).click(function () {
                $(this).val($(this).val() == '0' ? 1 : 0);
            });

            /**
             * email
             */
            if (this.$inputEmail.val()) {
                this.validateEmail(this.$inputEmail.val());
            }

            this.$inputEmail.change(function () {
                self.validateEmail($(this).val());
                $(this).next('.mage-error').hide();
            });

            /**
             * End init observer for email
             */

            this.initEventChangeAddress();
        },

        applyAddress: function (saveAddress) {
            var self = this, options = this.options;
            customCheckoutData.shippingAddressId(self.getSelectedAddressId());
            if (self.getSelectedAddressId() && self.customerAddressesJsonData[self.getSelectedAddressId()]) {
                self.hideNewFormAddress();
                selectShippingAddress(addressConverter.formAddressDataToQuoteAddress(self.customerAddressesJsonData[self.getSelectedAddressId()]));
            } else {
                selectShippingAddress(addressConverter.formAddressDataToQuoteAddress(self.getAddressData()));
                self.showNewFormAddress();
            }

            if(saveAddress) {
                saveShippingAddressAction(true);
            }
        },

        hideNewFormAddress: function () {
            this.$addressForm.hide();
        },

        showNewFormAddress: function () {
            this.$addressForm.show();
        },

        /**
         * init event for change address
         */
        initEventChangeAddress: function () {

            var self = this, options = this.options;
            var allFieldArray = ["country_id", "city", "postcode", "region_id", "region"];
            $(this.element).find(options.fieldSelector).filter('[required]').each(function() {
                if (allFieldArray.indexOf($(this).data('field-id')) == -1) {
                    allFieldArray.push($(this).data('field-id'));
                }
            });

            if (options.reloadSectionType == RELOAD_ON_ALL_FIELD_ARE_FILL) {
                $.each(allFieldArray, function (index, element) {
                    if (element == 'street') {
                        $(self.element).find('[name="shipping[street][]"]').change(function () {
                            if (self.validateRequireFields()) {
                                selectShippingAddress(addressConverter.formAddressDataToQuoteAddress(self.getAddressData()));
                                saveShippingAddressAction(true);
                                self.triggerEventChangeAddress();
                            }
                        });
                    } else {
                        $(self.element).find('[name$="[' + element + ']"]').change(function () {
                            if (self.validateRequireFields()) {
                                selectShippingAddress(addressConverter.formAddressDataToQuoteAddress(self.getAddressData()));
                                //saveShippingAddressAction(true);
                                self.triggerEventChangeAddress();
                            }
                        });
                    }
                });
            } else if (options.reloadSectionType == RELOAD_ON_ANY_TRIGGERING_ARE_CHANGE) {
                $.each(options.triggeringFieldsChange, function (index, element) {
                    $(self.element).find('[name$="[' + element + ']"]').change(function () {
                        selectShippingAddress(addressConverter.formAddressDataToQuoteAddress(self.getAddressData()));
                        saveShippingAddressAction(true);
                        self.triggerEventChangeAddress();
                    });
                });
            }
        },

        /**
         * check enable ajax update
         *
         * @returns {boolean}
         */
        enableAjaxUpdate: function () {
            return this.options.allowAjaxUpdate;
        },

        /**
         * trigger event change address
         */
        triggerEventChangeAddress: function (data) {
            if (this.enableAjaxUpdate()) {
                $(this.element).trigger(this.eventName, {
                    addressData: this.getAddressData()
                });
            }
        },

        /**
         * get selected address id in address book.
         *
         * @returns {*|jQuery}
         */
        getSelectedAddressId: function () {
            var $selectAddressId = $(this.element).find('.address-select');
            return $selectAddressId.length ? $selectAddressId.val() : '';
        },

        /**
         * init auto complete address
         */
        initAutoCompleteAddress: function () {
            var self = this, options = this.options;
            var streetElement = document.getElementById(this.type + ':street1');
            if (options.allowSuggestingAddress && streetElement) {
                try {
                    this.autocompleteAdress = new google.maps.places.Autocomplete(
                        (streetElement),
                        {types: ['geocode']}
                    );

                    google.maps.event.addListener(this.autocompleteAdress, 'place_changed', function () {
                        var locationInfo = self.exportLocationInfo(this.getPlace());
                        self.fillAddressForm(locationInfo);
                        console.log(1);
                        selectShippingAddress(addressConverter.formAddressDataToQuoteAddress(self.getAddressData()));
                        //saveShippingAddressAction(true);
                        self.triggerEventChangeAddress();

                    });

                } catch (e) {
                    console.trace(e);
                }

            }
        },

        /**
         * Export data location information.
         *
         * @param place
         * @returns {{street: *, city: *, region_id: *, region: *, country: *, postal_code: *, sublocality: *}}
         */
        exportLocationInfo: function (place) {
            var street, city, region_id, region, country, postcode, sublocality;

            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (this.addressFrom[addressType]) {
                    if (addressType == 'street_number') {
                        if (street)
                            street += ' ' + place.address_components[i][this.addressFrom['street_number']];
                        else
                            street = place.address_components[i][this.addressFrom['street_number']];
                    }
                    if (addressType == 'route') {
                        if (street)
                            street += ' ' + place.address_components[i][this.addressFrom['route']];
                        else
                            street = place.address_components[i][this.addressFrom['route']];
                    }
                    if (addressType == 'locality')
                        city = place.address_components[i][this.addressFrom['locality']];
                    if (addressType == 'administrative_area_level_1') {
                        region_id = place.address_components[i]['short_name'];
                        region = place.address_components[i]['long_name'];
                    }
                    if (addressType == 'country')
                        country = place.address_components[i][this.addressFrom['country']];
                    if (addressType == 'postal_code')
                        postcode = place.address_components[i][this.addressFrom['postal_code']];

                    if (addressType == 'sublocality_level_1')
                        sublocality = place.address_components[i][this.addressFrom['sublocality_level_1']];
                }
            }

            return {
                street: {
                    street1: street,
                    street2: sublocality,
                },
                city: city,
                region_id: region_id,
                region: region,
                country_id: country,
                postcode: postcode
            }
        },

        /**
         * Fill address form by location information
         *
         * @param locationInfo
         */
        fillAddressForm: function (locationInfo) {
            var self = this;

            /**
             * street
             * @type {*|jQuery}
             */
            var $street = $(this.element).find('[name$="[street][]"]');
            if (locationInfo.street.street1) {
                $street.eq(0).val(locationInfo.street.street1);
            }

            $street.eq(1).val(locationInfo.street.street2);

            /**
             * Country
             */
            $(this.element).find('[name$="[country_id]"]').val(locationInfo.country_id).trigger('change');

            /**
             * Region
             */
            $(this.element).find('[name$="[region]"]').val(locationInfo.region);
            $(this.element).find('[name$="[region_id]"]').find('*[data-region-code="' + locationInfo.region_id + '"]').prop('selected', true);

            /**
             * city
             */
            $(this.element).find('[name$="[city]"]').val(locationInfo.city);

            /**
             * zipcode
             */
            $(this.element).find('[name$="[postcode]"]').val(locationInfo.postcode);
        },

        /**
         * Check all require field is filled.
         *
         * return true if any required field is not filled
         *
         * @param selector
         * @returns {boolean}
         */
        validateRequireFields: function () {
            var self = this, options = this.options;
            var check = true;
            $(this.element).find(options.fieldSelector).filter('[required]').each(function () {
                var field = $(this);
                if (field.is(':visible') && !field.val()) {
                    check = false;
                    // stop each
                    return true;
                }
            });

            return check;
        },

        /**
         * Get address data by type shipping or billing
         *
         * @returns {{}}
         */
        getAddressData: function () {
            var self = this, options = this.options;
            var $addressElements = $(this.element).find(options.fieldSelector),
                $streetElements = $(this.element).find('[name$="[street][]"]'),
                $region = $(this.element).find('[name$="[region]"]'),
                $regionId = $(this.element).find('[name$="[region_id]"]'),
                addressData = {};

            $addressElements.filter(':not([name$="[street][]"])').each(function () {
                if (self.allowAddressFieldName.indexOf($(this).data('field-id')) != -1) {
                    if($(this).data('field-id') != 'region' && $(this).data('field-id') != 'region_id') {
                        addressData[$(this).data('field-id')] = $(this).val();
                    }
                }
            });

            if($region.is(':visible')) {
                addressData['region_id'] = '';
                addressData['region'] = $region.val();
            } else {
                addressData['region_id'] = $regionId.val();
                if(addressData['region_id']) {
                    addressData['region'] = $regionId.find('option:selected').html();
                }
            }

            addressData['street'] = [$streetElements.eq(0).val(), $streetElements.eq(1).val()];
            addressData['customer_address_id'] = this.getSelectedAddressId();

            return addressData;
        },

        /**
         * Validate email
         *
         * @param email
         */
        validateEmail: function (email) {
            var self = this, options = this.options;

            if (email) {
                storage.post(
                    'onestepcheckout/index/validateEmail',
                    JSON.stringify({
                        email: email
                    }),
                    false
                ).done(
                    function (result) {
                        if (typeof result.message_code != 'undefined') {
                            switch (result.message_code) {
                                case VALIDATE_EMAIL_INVALID:
                                    self.displayMessage(MESSAGE_INVALID_EMAIL);
                                    notifyCheckEmail.notifyInvalidEmail();
                                    customCheckoutData.isValidEmail(false);
                                    customCheckoutData.isExistEmail(false);
                                    self.$validateSuccessIcon.hide();
                                    self.$passwordSection.find('input').prop('disabled', 'disabled');
                                    break;
                                case VALIDATE_EMAIL_EXISTS:
                                    if(window.oneStepCheckoutConfig.showLoginLink == '1'){
                                        self.displayMessage(MESSAGE_EXISTS_EMAIL);
                                    }else{
                                        self.displayMessage(MESSAGE_EXISTS_EMAIL_NO_LOGIN_LINK);
                                    }
                                    self.$passwordSection.find('input').prop('disabled', 'disabled');
                                    self.$errorMessage.find('.login-link').click(function () {
                                        self.$loginLink.trigger('click');
                                    });
                                    //customCheckoutData.isValidEmail(false);
                                    customCheckoutData.isExistEmail(true);
                                    self.$validateSuccessIcon.hide();
                                    break;
                                default:
                                    self.$errorMessage.hide();
                                    self.$validateSuccessIcon.show().delay(3000).fadeOut(1);
                                    customCheckoutData.isValidEmail(true);
                                    customCheckoutData.isExistEmail(false);
                                    self.$passwordSection.find('input').removeAttr('disabled');
                                    break;
                            }
                        }
                    }
                ).fail(
                    function (result) {
                    }
                );
            }
        },

        displayMessage: function (message) {
            this.$errorMessage.show();
            this.$errorMessage.find('.content').html(message);

        },

        hideMessage: function () {
            self.$errorMessage.hide();
            this.$errorMessage.find('.content').html('');
        },


        notifyInvalidEmail: function () {
            this.$notifyEmail.show();
            this.$notifyEmailOverlay.show();
        },

        hideNotifyInvalidEmail: function () {
            this.$notifyEmail.hide();
            this.$notifyEmailOverlay.hide();
        },


    });

    return $.magestore.address;
});
