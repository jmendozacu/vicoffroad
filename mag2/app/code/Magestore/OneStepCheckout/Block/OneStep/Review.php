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

namespace Magestore\OneStepCheckout\Block\OneStep;

/**
 * Class Review
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Review extends \Magestore\OneStepCheckout\Block\AbstractOneStep
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_OneStepCheckout::review.phtml';

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * Review constructor.
     *
     * @param \Magestore\OneStepCheckout\Block\Context    $context
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param array                                       $data
     */
    public function __construct(
        \Magestore\OneStepCheckout\Block\Context $context,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_subscriberFactory = $subscriberFactory;
        $this->getQuote()->collectTotals()->save();
    }

    /**
     * @return mixed
     */
    public function enableSurvey()
    {
        return $this->_systemConfig->enableSurvey();
    }

    /**
     * @return mixed
     */
    public function getSurveyQuestion()
    {
        return $this->_systemConfig->getSurveyQuestion();
    }

    /**
     * @return mixed
     */
    public function getSurveyValues()
    {
        return unserialize($this->_systemConfig->getSurveyValues());
    }

    /**
     * @return mixed
     */
    public function enableFreeText()
    {
        return $this->_systemConfig->enableFreeText();
    }

    /**
     * @return bool
     */
    public function enableGiftMessage()
    {
        return $this->_systemConfig->enableGiftMessage();
    }

    /**
     * @return bool
     */
    public function isSignUpNewsletter()
    {
        if ($this->isCustomerLoggedIn()) {
            $customer = $this->_customerSession->getCustomer();
            if (isset($customer))
                $customerNewsletter = $this->_subscriberFactory->create()->loadByEmail($customer->getEmail());
            if (isset($customerNewsletter) && $customerNewsletter->getId() != NULL &&
                $customerNewsletter->getData('subscriber_status') == 1
            ) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * @return bool
     */
    public function isShowNewsletter()
    {
        $setting = $this->_systemConfig->isShowNewsletter();
        if ($setting && !$this->isSignUpNewsletter()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return mixed
     */
    public function isSubscribeByDefault()
    {
        return $this->_systemConfig->isSubscribeByDefault();
    }

    /**
     * @return mixed
     */
    public function isShowComment()
    {
        return $this->_systemConfig->isShowComment();
    }

    /**
     * @return mixed
     */
    public function isShowTermCondition()
    {
        return $this->_systemConfig->isShowTermCondition();
    }

    /**
     * @return mixed
     */
    public function isShowDiscount()
    {
        return $this->_systemConfig->isShowDiscount();
    }

    /**
     * @return mixed
     */
    public function isEnableGiftWrap()
    {
        return $this->_systemConfig->isEnableGiftWrap();
    }

    /**
     * @return mixed
     */
    public function getGiftWrapAmount()
    {
        return $this->_systemConfig->getGiftWrapAmount();
    }

    /**
     * @return float|int|mixed
     */
    public function getOrderGiftWrapAmount()
    {
        return $this->getOneStepHelper()->getOrderGiftWrapAmount();
    }

    /**
     * @return mixed
     */
    public function checkGiftWrapSession()
    {
        return $this->_checkoutSession->getData('onestepcheckout_giftwrap');
    }

    /**
     * @return mixed
     */
    public function getGiftWrapType()
    {
        return $this->_systemConfig->getGiftWrapType();
    }

}