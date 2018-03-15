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
        return {
            stringToHash: function (shippingMethod) {
                var rate = shippingMethod.split('|');
                if (rate[0] && rate[1]) {
                    return {
                        carrier_code: rate[0],
                        method_code: rate[1]
                    };
                } else {
                    return null;
                }
            },

            hashToString: function (shippingMethod) {
                if(shippingMethod) {
                    return shippingMethod.carrier_code + '_' + shippingMethod.method_code;
                }

                return '';
            }
        }
    }
);
