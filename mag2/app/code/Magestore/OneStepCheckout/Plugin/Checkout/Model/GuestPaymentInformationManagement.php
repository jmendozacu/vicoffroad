<?php

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

namespace Magestore\OneStepCheckout\Plugin\Checkout\Model;

use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * class GuestPaymentInformationManagement
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class GuestPaymentInformationManagement extends \Magestore\OneStepCheckout\Plugin\AbstractOneStepPlugin
{
    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param \Closure                                             $proceed
     * @param                                                      $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface             $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|NULL        $billingAddress
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = NULL
    )
    {
        /** @var \Magento\Framework\DataObject $additionalData */
        $additionalData = $this->_getAdditionalDataObject();

        try {
            $this->_checkoutRegister($cartId, $email, $billingAddress);
            $orderId = $proceed($cartId, $email, $paymentMethod, $billingAddress);

            return $orderId;
        } catch (\Exception $e) {
            $this->_messageManager->addError(
                $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
            );
        }
    }

    /**
     * Register account when checkout as guest
     *
     * @param                                               $cartId
     * @param                                               $email
     * @param \Magento\Framework\DataObject                 $oneStepData
     * @param \Magento\Quote\Api\Data\AddressInterface|NULL $billingAddress
     */
    protected function _checkoutRegister(
        $cartId,
        $email,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = NULL
    )
    {
        /** @var \Magento\Framework\DataObject $additionalData */
        $additionalData = $this->_getAdditionalDataObject();

        $canRegister = $this->checkCanRegister(
            $additionalData->getData('create_account_checkbox'),
            $email,
            $additionalData->getData('customer_password'),
            $additionalData->getData('confirm_password')
        );

        if ($canRegister) {
            $storeId = $this->_storeManager->getStore()->getId();
            $this->getOnePage()->saveCheckoutMethod(Onepage::METHOD_REGISTER);
            try {
                $customer = $this->_createCustomer($email, $billingAddress);
                $this->_cartManagement->assignCustomer($cartId, $customer->getId(), $storeId);
                $this->_customerSession->setCustomerAsLoggedIn($customer);
            } catch (\Exception $e) {
                $this->_messageManager->addError(
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                );
                $this->_logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * @param $checkCreateAccount
     * @param $email
     * @param $customerPassword
     * @param $confirmPassword
     *
     * @return bool
     */
    public function checkCanRegister($checkCreateAccount, $email, $customerPassword, $confirmPassword)
    {
        return $this->_systemConfig->enableRegistration()
        && $checkCreateAccount
        && !$this->_oneStepHelper->isContainDownloadableProduct()
        && $this->_validator->validateEmail($email)
        && $this->_validator->validatePassword($customerPassword, $confirmPassword);
    }

    /**
     * @param                                          $email
     * @param                                          $password
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _createCustomer(
        $email,
        \Magento\Quote\Api\Data\AddressInterface $address
    )
    {
        /** @var \Magento\Framework\DataObject $additionalData */
        $additionalData = $this->_getAdditionalDataObject();

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_customerFactory->create([
            'data' => [
                CustomerInterface::FIRSTNAME    => $address->getFirstname(),
                CustomerInterface::LASTNAME     => $address->getLastname(),
                CustomerInterface::EMAIL        => $email,
                CustomerInterface::WEBSITE_ID   => $this->_storeManager->getStore()->getWebsiteId(),
                CustomerInterface::STORE_ID     => $this->_storeManager->getStore()->getId(),
                'password'                      => $additionalData->getData('customer_password'),
                CustomerInterface::CONFIRMATION => $additionalData->getData('confirm_password'),
            ],
        ]);

        if ($address->getMiddlename()) {
            $customer->setData(CustomerInterface::MIDDLENAME, $address->getMiddlename());
        }

        if ($address->getPrefix()) {
            $customer->setData(CustomerInterface::PREFIX, $address->getPrefix());
        }

        if ($address->getSuffix()) {
            $customer->setData(CustomerInterface::SUFFIX, $address->getSuffix());
        }

        if ($address->getVatId()) {
            $customer->setData(CustomerInterface::TAXVAT, $address->getVatId());
        }

        if ($additionalData->getData(CustomerInterface::GENDER)) {
            $customer->setData(CustomerInterface::GENDER, $additionalData->getData(CustomerInterface::GENDER));
        }

        if ($additionalData->getData(CustomerInterface::DOB)) {
            $customer->setData(CustomerInterface::DOB, $additionalData->getData(CustomerInterface::DOB));
        }

        $customer->save()->sendNewAccountEmail();

        return $customer;
    }


    /**
     * Get additional data.
     *
     * @return mixed
     */
    protected function _getAdditionalDataObject()
    {
        /** @var \Magento\Framework\DataObject $oneStepData */
        $additionalData = $this->_checkoutSession->getData('additional_data');

        if (!$this->_additionalDataObject) {
            $this->_additionalDataObject = $this->_dataObjectFactory->create([
                'data' => is_array($additionalData) ? $additionalData : [],
            ]);
        }

        return $this->_additionalDataObject;
    }
}