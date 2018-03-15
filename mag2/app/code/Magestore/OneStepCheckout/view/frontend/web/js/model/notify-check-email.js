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
    [
        'jquery',
        "underscore",
        'ko'
    ],
    function ($, _, ko) {
        'use strict';
        var  $notifyEmail = $('#notify-email-invalid'),
            $notifyEmailOverlay = $('#notify-email-invalid-overlay');

        function notifyInvalidEmail() {
            $notifyEmail.show();
            $notifyEmailOverlay.show();
        }

        function hideNotifyInvalidEmail() {
            $notifyEmail.hide();
            $notifyEmailOverlay.hide();
        }

        $notifyEmailOverlay.click(function () {
            hideNotifyInvalidEmail();
        });

        return {
            notifyInvalidEmail: notifyInvalidEmail,
            hideNotifyInvalidEmail: hideNotifyInvalidEmail,
        };
    }
);
