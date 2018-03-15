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
 * Class OneStep
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class OneStep extends \Magestore\OneStepCheckout\Block\AbstractOneStep
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_OneStepCheckout::onestep.phtml';
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * @var \Magento\Checkout\Model\CompositeConfigProvider
     */
    protected $configProvider;

    /**
     * @var array|\Magento\Checkout\Block\Checkout\LayoutProcessorInterface[]
     */
    protected $_layoutProcessors;

    /**
     * @param Context                                         $context
     * @param \Magento\Directory\Helper\Data                  $directoryHelper
     * @param \Magento\Checkout\Model\CompositeConfigProvider $configProvider
     * @param array                                           $data
     */
    public function __construct(
        \Magestore\OneStepCheckout\Block\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Checkout\Model\CompositeConfigProvider $configProvider,
        array $layoutProcessors = [],
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_directoryHelper = $directoryHelper;
        $this->configProvider = $configProvider;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->_layoutProcessors = $layoutProcessors;
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->_layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }

        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * @return string
     */
    public function getOneStepCheckoutConfig()
    {
        return \Zend_Json::encode([
            'saveAddressUrl'               => 'onestepcheckout/index/saveAddressOneStep',
            'saveShippingMethodUrl'        => 'onestepcheckout/index/saveShippingMethod',
            'savePaymentMethodUrl'         => 'onestepcheckout/index/savePaymentMethod',
            'saveCustomCheckoutData'       => 'onestepcheckout/index/saveCustomCheckoutData',
            'validateEmailUrl'             => 'onestepcheckout/index/validateEmail',
            'editQtyUrl'                   => 'onestepcheckout/quote/editQty',
            'deleteItemUrl'                => 'onestepcheckout/quote/deleteItem',
            'updateOnChangeAddress'        => $this->_systemConfig->getSectionUpdateOnChangeAddress(),
            'updateOnChangeShippingMehtod' => $this->_systemConfig->getSectionUpdateOnChangeShippingMehtod(),
            'updateOnChangePaymentMehtod'  => $this->_systemConfig->getSectionUpdateOnChangeShippingMehtod(),
            'customerAddressesJsonData'    => $this->getCustomerAddressesData(),
            'addressConfig'                => [
                'regionJson'             => $this->_directoryHelper->getRegionData(),
                'defaultRegion'          => $this->_systemConfig->getDefaultRegionId(),
                'requireFields'          => $this->getRequireFields(),
                'triggeringFieldsChange' => $this->_systemConfig->getTriggeringFieldsChange(),
                'reloadSectionType'      => $this->_systemConfig->getReloadSectionType(),
                'allowAjaxUpdate'        => $this->_systemConfig->isAjaxUpdateOnChangeAddress(),
                'allowSuggestingAddress' => $this->_systemConfig->allowSuggestingAddress(),
            ],
            'defaultPaymentMethodCode'   => $this->_systemConfig->getDefaultPaymentMethod(),
            'showLoginLink'              => $this->_systemConfig->_getCheckoutModeConfig('show_login_link'),
            'api_key'                    => $this->_systemConfig->_getCheckoutModeConfig('google_api_key')
        ]);
    }

    /**
     * @param $urlPath
     *
     * @return string
     */
    public function getAjaxUrl($urlPath)
    {
        return $this->_oneStepHelper->getAjaxUrl($urlPath);
    }

    /**
     * Get region json data
     *
     * @return string
     */
    public function getRegionJson()
    {
        return $this->_directoryHelper->getRegionJson();
    }

    /**
     * @return mixed
     */
    public function getCartId()
    {
        return $this->getQuote()->getId();

    }

    /**
     * @return array
     */
    public function getCheckoutConfig()
    {
        return $this->configProvider->getConfig();
    }

    /**
     * @return mixed
     */
    public function getCheckoutDescription()
    {
        return $this->_systemConfig->getOneStepDescription();
    }

    /**
     * @return mixed
     */
    public function getOneStepTitle()
    {
        return $this->_systemConfig->getOneStepTitle();
    }

    /**
     * @return bool
     */
    public function isShowDelivery()
    {
        return ($this->_systemConfig->isShowDelivery() && !$this->isVirtualQuote());
    }

    /**
     * @return mixed
     */
    public function getLoginTitle()
    {
        return $this->_systemConfig->getLoginLinkTitle();
    }

    /**
     * @return bool
     */
    public function enableBillingDifferentAddress()
    {
        return $this->_systemConfig->enableBillingDifferentAddress();
    }

    /**
     *
     */
    public function clearParamsSession()
    {
        $this->_checkoutSession->setOscQuestion(NULL);
        $this->_checkoutSession->setOscAnswer(NULL);
        $this->_checkoutSession->setOscComment(NULL);
        $this->_checkoutSession->setOscSubscriber(NULL);
        $this->_checkoutSession->setOscNoAllowMessage(NULL);
        $this->_checkoutSession->setOscFrom(NULL);
        $this->_checkoutSession->setOscTo(NULL);
        $this->_checkoutSession->setOscMessage(NULL);
        $this->_checkoutSession->setDateDelivery(NULL);
        $this->_checkoutSession->setTimeDelivery(NULL);
    }

    /**
     * @return bool
     */
    public function canShowLoginLink()
    {
        if (!$this->isCustomerLoggedIn() && $this->_systemConfig->isShowLoginLink()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getCustomerAddresses()
    {
        return $this->getCustomer()->getAddresses();
    }

    /**
     * @return string
     */
    public function getCustomerAddressesData()
    {
        $result = [];

        foreach ($this->getCustomerAddresses() as $address) {
            $addressData = $address->getData();
            $addressData['street'] = [$address->getData('street')];
            $result[$address->getId()] = $addressData;
        }

        return $result;
    }

}