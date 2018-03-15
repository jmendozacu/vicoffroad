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

/*jshint jquery:true*/
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define([
            "jquery",
            "mage/validation",
            "mage/translate",
            "mage/mage",
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {
    "use strict";

    $.validator.addMethod('validate-require-one-shipping-method', function (value, element) {
        if ($('.validate-require-one-shipping-method:checked').length > 0) {
            $(element).parents('.sp-methods').next('.mage-error').remove();
            return true;
        }

        return false;
    }, $.mage.__('Please select one of the options.'));

    $.validator.addMethod('validate-require-one-payment-method', function (value, element) {
        if ($('.validate-require-one-payment-method:checked').length > 0) {
            $(element).parents('.sp-methods').next('.mage-error').remove();
            return true;
        }

        return false;
    }, $.mage.__('Please select one of the options.'));

    return $.mage.validation;
}));