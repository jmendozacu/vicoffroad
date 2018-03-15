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

namespace Magestore\OneStepCheckout\Plugin;

/**
 * class AbstractOneStepPlugin
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
abstract class AbstractOneStepPlugin extends \Magestore\OneStepCheckout\Plugin\AbstractPlugin
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magestore\OneStepCheckout\Model\Validator
     */
    protected $_validator;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Quote\Api\GuestCartManagementInterface
     */
    protected $_cartManagement;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\DataObject|null
     */
    protected $_additionalDataObject = NULL;

    /**
     * AbstractOnepagePlugin constructor.
     *
     * @param \Magestore\OneStepCheckout\Plugin\Context        $context
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magestore\OneStepCheckout\Model\Validator       $validator
     * @param \Magento\Customer\Model\CustomerFactory          $customerFactory
     * @param \Magento\Quote\Api\GuestCartManagementInterface  $cartManagement
     * @param \Magestore\OneStepCheckout\Model\DeliveryFactory $deliveryFactory
     * @param \Magestore\OneStepCheckout\Model\SurveyFactory   $surveyFactory
     * @param \Psr\Log\LoggerInterface                         $logger
     */
    public function __construct(
        \Magestore\OneStepCheckout\Plugin\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\OneStepCheckout\Model\Validator $validator,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Quote\Api\GuestCartManagementInterface $cartManagement,
        \Magestore\OneStepCheckout\Model\DeliveryFactory $deliveryFactory,
        \Magestore\OneStepCheckout\Model\SurveyFactory $surveyFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        parent::__construct($context);

        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_validator = $validator;
        $this->_customerFactory = $customerFactory;
        $this->_cartManagement = $cartManagement;
        $this->_logger = $logger;
        $this->_deliveryFactory = $deliveryFactory;
        $this->_surveyFactory = $surveyFactory;
    }

    /**
     * Check customer is loggedin.
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }
}