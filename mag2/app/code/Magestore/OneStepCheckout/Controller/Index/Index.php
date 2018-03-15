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

namespace Magestore\OneStepCheckout\Controller\Index;

/**
 * Class Index
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Index extends \Magestore\OneStepCheckout\Controller\Index
{

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->_systemConfig->isEnableOneStepCheckout()) {
            return $resultRedirect->setPath('checkout');
        }

        if (!$this->_systemConfig->isCustomerLogin() && !$this->_systemConfig->isAllowGuest()) {
            $this->messageManager->addNotice(__('Please login to checkout'));

            return $resultRedirect->setPath('customer/account/login');
        }

        $this->getOnePage()->initCheckout();
        $this->_initShippingAddress($this->getShippingAddress());

        $quote = $this->getQuote();

        if (!$quote->hasItems() || !empty($quote->getErrors())) {
            return $resultRedirect->setPath('checkout/cart');
        }

        if (!$quote->validateMinimumAmount()) {
            $this->messageManager->addError(
                $this->_systemConfig->getConfig('sales/minimum_order/error_message')
            );

            return $resultRedirect->setPath('checkout/cart');
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }

    /**
     * Init default data for shipping address
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     */
    protected function _initShippingAddress(\Magento\Quote\Model\Quote\Address $address)
    {
        if (!$address->getData('country_id')) {
            $address->setData('country_id', $this->_systemConfig->getDefaultCountryId());
        }

        if (!$address->getData('postcode')) {
            $address->setData('postcode', $this->_systemConfig->getDefaultPostcode());
        }

        if (!$address->getData('region_id')) {
            $address->setData('region_id', $this->_systemConfig->getDefaultRegionId());
        }

        if (!$address->getData('city')) {
            $address->setData('city', $this->_systemConfig->getDefaultCity());
        }

        if ($address->hasDataChanges()) {
            $address->setCollectShippingRates(TRUE)->save();
        }
    }
}
