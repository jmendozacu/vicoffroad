define(
    [
        'jquery',
        'Magestore_OneStepCheckout/js/action/save-shipping-address',
        'Magestore_OneStepCheckout/js/action/get-payment-information',
        'mage/storage',
        'Magestore_OneStepCheckout/js/order-review',
        'Magestore_OneStepCheckout/js/shipping-method',
    ],
    function ($, saveShippingAddressAction, getPaymentInformation, storage) {
        'use strict';
        var miniCart, block_minicart;

        miniCart = $('[data-block=\'minicart\']');
        block_minicart = $('.block-minicart');
        window.contentUpdatedCart = false;

        return function () {

            miniCart.on('click', 'button.update-cart-item', function () {
                window.contentUpdatedCart = true;
            });
            miniCart.on('click', 'a.action.delete', function () {
                window.contentUpdatedCart = true;
            });

            miniCart.on('contentUpdated', function(event) {
                if(block_minicart.is(":visible")) {
                    block_minicart.css('z-index', '9999');
                    // console.log(window.contentUpdateCart);
                    if (quote.isVirtual()) {
                        //window.location.reload();
                        if (window.contentUpdatedCart == true) {
                            var params = {};
                            storage.post(
                                'onestepcheckout/quote/reloadAddressReview',
                                JSON.stringify(params),
                                false
                            ).done(
                                function (result) {
                                    if (result.review_info) {
                                        $('#checkout-review-load').orderReview('updateOrderReview', result.review_info);
                                    }
                                    if (result.shipping_method) {
                                        $('#shipping-method-wrapper').shippingMethod('updateShippingMethodInfo', result.shipping_method);
                                    }
                                    if (result.giftWrap_amount) {
                                        $('.onestepcheckout-giftwrap .price').html(result.giftWrap_amount);
                                    }
                                    if (result.payment_method) {
                                        getPaymentInformation();
                                    }
                                    if (result.empty_quote) {
                                        setTimeout(function () {
                                            window.location.href = url.build('checkout/cart/index');
                                        }, 7000);
                                    }

                                    /*if (result.is_virtual) {
                                     window.location.reload();
                                     }*/
                                }
                            ).fail(
                                function (result) {
                                }
                            ).always(
                                function (response) {
                                }
                            );
                        }
                    }else {
                        if (window.contentUpdatedCart == true) {
                            saveShippingAddressAction(true);
                        }
                    }
                    window.contentUpdatedCart = false;
                }
            });
        };
    }
);
