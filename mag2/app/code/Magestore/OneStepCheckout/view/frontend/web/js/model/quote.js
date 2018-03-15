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

define(
    ['ko'],
    function (ko) {
        'use strict';
        var billingAddress = ko.observable(null);
        var shippingAddress = ko.observable(null);
        var shippingMethod = ko.observable(null);
        var paymentMethod = ko.observable(null);
        var guestEmail = ko.observable(null);
        var additionalData = ko.observable(null);
        var quoteData = window.checkoutConfig.quoteData;
        var basePriceFormat = window.checkoutConfig.basePriceFormat;
        var priceFormat = window.checkoutConfig.priceFormat;
        var storeCode = window.checkoutConfig.storeCode;
        var totalsData = window.checkoutConfig.totalsData;
        var totals = ko.observable(totalsData);
        var collectedTotals = ko.observable({});
        return {
            totals: totals,
            shippingAddress: shippingAddress,
            shippingMethod: shippingMethod,
            billingAddress: billingAddress,
            paymentMethod: paymentMethod,
            guestEmail: guestEmail,
            additionalData: additionalData,

            getQuoteId: function () {
                return quoteData.entity_id;
            },
            isVirtual: function () {
                return !!Number(quoteData.is_virtual);
            },
            getPriceFormat: function () {
                return priceFormat;
            },
            getBasePriceFormat: function () {
                return basePriceFormat;
            },
            getItems: function () {
                return window.checkoutConfig.quoteItemData;
            },
            getTotals: function () {
                return totals;
            },
            setTotals: function (totalsData) {
                if (_.isObject(totalsData.extension_attributes)) {
                    _.each(totalsData.extension_attributes, function (element, index) {
                        totalsData[index] = element;
                    });
                }
                totals(totalsData);
                this.setCollectedTotals('subtotal_with_discount', parseFloat(totalsData.subtotal_with_discount));
            },
            setPaymentMethod: function (paymentMethodCode) {
                paymentMethod(paymentMethodCode);
            },
            getPaymentMethod: function () {
                return paymentMethod;
            },
            getStoreCode: function () {
                return storeCode;
            },
            setCollectedTotals: function (code, value) {
                var totals = collectedTotals();
                totals[code] = value;
                collectedTotals(totals);
            },
            getCalculatedTotal: function () {
                var total = 0.;
                _.each(collectedTotals(), function (value) {
                    total += value;
                });
                return total;
            },

            setShippingMethod: function (shippingMethod) {
                var rate = shippingMethod.split('|');
                if (rate[0] && rate[1]) {
                    this.shippingMethod({
                        carrier_code: rate[0],
                        method_code: rate[1]
                    });
                } else {
                    this.shippingMethod(null);
                }
            },

            getShippingMethod: function () {
                if(this.shippingMethod()){
                    return this.shippingMethod().carrier_code + '_' + this.shippingMethod().method_code;
                }
            }
        };
    }
);
