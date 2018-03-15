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
        'Magestore_OneStepCheckout/js/model/custom-checkout-data',
        'mage/storage',
        'mage/url',
        'Magestore_OneStepCheckout/js/model/full-screen-loader',
    ],
    function (
        $,
        _,
        customCheckoutData,
        storage,
        fullScreenLoader
    ) {
        'use strict';

        return function () {
            if (quote.isVirtual()) {
                return;
            }
            var serviceUrl = window.oneStepCheckoutConfig.saveCustomCheckoutData,
                payload = {
                    additional_data: customCheckoutData.getAdditionalData()
                };

            loadingManager.startLoaderAll();
            return storage.post(
                serviceUrl, JSON.stringify(payload), false
            ).done(
                function (response) {
                    customCheckoutData.isSavedCheckoutData(true);
                    oadingManager.stopLoaderAll();
                }
            ).fail(
                function (response) {
                    loadingManager.stopLoaderAll();
                }
            );
        };
    }
);
