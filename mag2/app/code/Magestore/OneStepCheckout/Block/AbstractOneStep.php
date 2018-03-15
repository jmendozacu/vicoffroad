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

namespace Magestore\OneStepCheckout\Block;

/**
 * Class AbstractOneStep
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class AbstractOneStep extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magestore\OneStepCheckout\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote;

    /**
     * @var \Magestore\OneStepCheckout\Helper\Data
     */
    protected $_oneStepHelper;

    /**
     * constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magestore\OneStepCheckout\Block\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->_objectManager = $context->getObjectManager();
        $this->_systemConfig = $context->getSystemConfig();
        $this->_checkoutSession = $context->getCheckoutSession();
        $this->_customerSession = $context->getCustomerSession();
        $this->_moduleManager = $context->getModuleManager();
        $this->_oneStepHelper = $context->getOneStepHelper();
    }

    /**
     * @return \Magestore\OneStepCheckout\Helper\Data
     */
    public function getOneStepHelper()
    {
        return $this->_oneStepHelper;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_systemConfig->isEnableOneStepCheckout()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Retrieve sales quote model.
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = $this->getCheckoutSession()->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Retrieve checkout session model
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    /**
     * Get logged in customer.
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = $this->_customerSession->getCustomer();
        }

        return $this->_customer;
    }

    /**
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    public function getOnePage()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Type\Onepage');
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

    /**
     * @return \Magestore\OneStepCheckout\Model\SystemConfig
     */
    public function getSystemConfig()
    {
        return $this->_systemConfig;
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getBillingAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address|mixed
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }


    /**
     * @return array
     */
    public function getRequireFields()
    {
        return $this->_systemConfig->getListReqiredFields();
    }

    /**
     * @return mixed
     */
    public function enableRegistration()
    {
        return $this->_systemConfig->enableRegistration();
    }

    /**
     * @return bool
     */
    public function allowGuestCheckout()
    {
        return $this->_systemConfig->allowGuestCheckout() && !$this->_oneStepHelper->isContainDownloadableProduct();
    }

    /**
     * @return bool
     */
    public function isVirtualQuote()
    {
        return $this->getOnePage()->getQuote()->isVirtual();
    }

    /**
     * @return \DateTime
     */
    public function getCurrentTime()
    {
        return $this->_localeDate->date();
    }
}
