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

namespace Magestore\OneStepCheckout\Model;

/**
 * class Validator
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Validator
{
    const VALIDATE_EMAIL_VALID = 1;
    const VALIDATE_EMAIL_EXISTS = 0;
    const VALIDATE_EMAIL_INVALID = -1;

    /**
     * @var \Zend_Validate_EmailAddress
     */
    protected $_validateEmail;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * ValidateEmailAddress constructor.
     *
     * @param \Zend_Validate_EmailAddress $validateEmail
     */
    public function __construct(
        \Zend_Validate_EmailAddress $validateEmail,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_validateEmail = $validateEmail;
        $this->_customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Validate customer email
     *
     * @param $email
     *
     * @return int
     */
    public function validateEmail($email)
    {
        if (!$email || !$this->_validateEmail->isValid($email)) {
            return self::VALIDATE_EMAIL_INVALID;
        }

        return $this->isExistEmail($email) ? self::VALIDATE_EMAIL_EXISTS : self::VALIDATE_EMAIL_VALID;
    }

    /**
     * Check customer email exist
     *
     * @param $email
     *
     * @return mixed
     */
    public function isExistEmail($email)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_customerFactory->create([
            'data' => [
                'website_id' => $this->_storeManager->getStore()->getWebsiteId(),
            ],
        ])->loadByEmail($email);

        return $customer->getId();
    }

    /**
     * @param $password
     * @param $confirmPassword
     */
    public function validatePassword($password, $confirmPassword)
    {
        return $password && $confirmPassword && $password == $confirmPassword;
    }
}