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
    'Magestore_OneStepCheckout/js/model/full-screen-loader',
    'Magento_Ui/js/modal/confirm',
    'jquery/ui',
    'Magestore_OneStepCheckout/js/ajax-loading'
], function ($, storage, fullScreenLoader, confirmation) {
    $.widget("magestore.orderReview", $.magestore.ajaxLoading, {
        options: {
            adjustQtyUrl: ''
        },

        /**
         * orderReview creation
         * @protected
         */

        _create: function () {
            var self = this, options = this.options;
            $.extend(this, {
                overlaySelector: '#control_overlay_review',
                loaderSelector: '.ajax-loader-order-review'
            });

            this.updateObserver();
        },

        updateObserver: function () {
            var self = this, options = this.options;

            $(self.element).find('.osc-add,.osc-minus').click(function () {
                var $boxQty = $(this).closest('.box-qty'),
                    $itemQtyDisplay = $boxQty.find('.qty-item-display'),
                    $itemInput = $boxQty.find('.qty-item'),
                    oldValue = parseInt($itemInput.val());

                if ($(this).data('change-type') == 'add') {
                    $itemQtyDisplay.html(oldValue + 1);
                    $itemInput.val(oldValue + 1);
                } else {
                    if (parseInt($itemInput.val()) > 0) {
                        $itemQtyDisplay.html(oldValue - 1);
                        $itemInput.val(oldValue - 1);
                    }
                }

                /**
                 * event change change Qty
                 */
                $(self.element).trigger('changeQty', {
                    itemId: $boxQty.data('item-id'),
                    qty: $itemInput.val()
                });
            });

            $(self.element).find('.osc-delete').click(function () {
                var itemId = $(this).data('item-id');
                confirmation({
                    title: '',
                    content: 'Are you sure you would like to remove this item from the shopping cart?',
                    actions: {
                        confirm: function(){
                            $(self.element).trigger('deleteItem', {
                                itemId: itemId
                            });
                        },
                        cancel: function(){},
                        always: function(){}
                    }
                });
            });

            $('#onestepcheckout_giftwrap_checkbox').click(function () {
                var isWrap;
                var wrapAmount;
                $(self.element).trigger('giftWrap', {
                    isWrap: isWrap,
                    wrapAmount: wrapAmount
                });

            });
        },

        /**
         * Update order view
         *
         * @param reviewInfo
         */
        updateOrderReview: function (reviewInfo) {
            $(this.element).html(reviewInfo);
            this.updateObserver();
        }

    });

    return $.magestore.orderReview;

});