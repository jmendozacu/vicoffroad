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
    'jquery/ui'
], function ($) {
    $.widget("magestore.ajaxLoading", {
        options: {},

        /**
         * paymentMethod creation
         * @protected
         */
        _create: function () {
            var self = this, options = this.options;

            $.extend(this, {
                overlaySelector: '',
                loaderSelector: ''
            });
        },

        /**
         * Show overlay
         */
        showOverlay: function () {
            $(this.overlaySelector).show();
        },

        /**
         * Hide overlay
         */
        hideOverlay: function () {
            $(this.overlaySelector).hide();
        },

        /**
         * show loader
         */
        startLoader: function () {
            $(this.loaderSelector).show();
        },

        /**
         * hide loader
         */
        stopLoader: function () {
            $(this.loaderSelector).hide();
        },
    });


    return $.magestore.ajaxLoading;

});