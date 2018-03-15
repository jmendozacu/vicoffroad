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
        'ko',
        'Magento_Checkout/js/model/quote',
    ],
    function ($, _, ko, quote) {
        'use strict';
        return {
            additionalData: ko.observable(null),
            shippingAddressId: ko.observable(null),
            guestEmail: ko.observable(null),
            isValidEmail: ko.observable(true),
            isExistEmail: ko.observable(false),
            isSavedCheckoutData: ko.observable(false),

            /**
             * Get delivery date time data
             *
             * @returns {{osc_delivery_date: *, osc_delivery_time: *}}
             */
            getDeliveryDateTimeData: function () {
                var $deliveryDate = $('#delivery_date'),
                    $deliveryTime = $('#delivery-time');

                return {
                    osc_delivery_date: $deliveryDate.length ? $deliveryDate.val() : '',
                    osc_delivery_time: $deliveryTime.length ? $deliveryTime.val() : ''
                }
            },

            /**
             * get password data for create account
             *
             * @returns {{create_account_checkbox: (*|jQuery), customer_password: (*|jQuery), confirm_password: (*|jQuery)}}
             */
            getPasswordData: function () {
                var $accountCheck = $('.create-account-checkbox'),
                    $customerPassword = $('.customer-password'),
                    $confirmPassword = $('.confirm-password');

                return {
                    create_account_checkbox: $accountCheck.length ? $accountCheck.val() : '',
                    customer_password: $customerPassword.length ? $customerPassword.val() : '',
                    confirm_password: $confirmPassword.length ? $confirmPassword.val() : ''
                }
            },

            /**
             * Get question and answer data
             *
             * @returns {{osc_question: *, osc_answer: *}}
             */
            getQuestionAndAnswerData: function () {
                var answerObject = $('#id_survey');
                var otherAnswerObject = $('#id_survey_freetext');
                var questionObject = $('.onestepcheckout-survey label');
                var answer = '', question = '';

                if (answerObject.length && questionObject.length) {
                    var optionSelected = answerObject.find("option:selected");
                    if (optionSelected.val() != '') {
                        answer = optionSelected.text();
                    }
                    question = questionObject.html();
                    if (otherAnswerObject && otherAnswerObject.is(':visible')) {
                        answer = otherAnswerObject.val();
                    }
                }

                return {
                    osc_question: question,
                    osc_answer: answer,
                }
            },

            /**
             * Get onestepcheckout message data
             *
             * @returns {{osc_to_message: *, osc_from_message: *, osc_message: *}}
             */
            getMessageData: function () {
                var fromMessageObject = $('#gift-message-whole-from'),
                    toMessageObject = $('#gift-message-whole-to'),
                    messageObject = $('#gift-message-whole-message'),
                    allowMessage = $('#allow_gift_messages'),
                    fromMessage = '',
                    toMessage = '',
                    message = '';

                if (fromMessageObject.length && toMessageObject.length
                    && messageObject.length && allowMessage.length
                    && allowMessage.prop('checked')
                ) {
                    fromMessage = fromMessageObject.val();
                    toMessage = toMessageObject.val();
                    message = messageObject.val();
                }

                return {
                    osc_to_message: toMessage,
                    osc_from_message: fromMessage,
                    osc_message: message
                }

            },

            /**
             * Get Order comment
             *
             * @returns {{osc_comment: string}}
             */
            getOrderCommentData: function () {
                var $order = $('#onestepcheckout_comment');
                return {
                    osc_comment: $order.length ? $order.val() : ''
                }
            },

            /**
             * Get additional customer data.
             * Some data ie. gender, dob can not use in shipping address and billing address with REST API.
             * It only can add to additional data.
             *
             * @returns {{}}
             */
            getCustomerAddtionalData: function () {
                var customerData = {},
                    $gender = $('[name="shipping[gender]"]'),
                    $dob = $('[name="shipping[dob]"]');

                if ($gender.val()) {
                    customerData.gender = $gender.val();
                }

                if ($dob.val()) {
                    customerData.dob = $dob.val();
                }

                return customerData;
            },

            /**
             * Get additional data
             *
             * @returns {{}}
             */
            getAdditionalData: function () {
                var additionalData = {},
                    $subscriber = $('#newsletter_subscriber_checkbox');

                _.extend(
                    additionalData,
                    this.getDeliveryDateTimeData(),
                    this.getPasswordData(),
                    this.getQuestionAndAnswerData(),
                    this.getMessageData(),
                    this.getOrderCommentData(),
                    this.getCustomerAddtionalData(),
                    {
                        is_subscriber: $subscriber.prop('checked') ? 1 : 0,
                        email: quote.guestEmail
                    }
                );
                return additionalData;
            },
        };
    }
);
