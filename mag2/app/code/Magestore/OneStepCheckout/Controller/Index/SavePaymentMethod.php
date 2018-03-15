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
 *
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class SavePaymentMethod extends \Magestore\OneStepCheckout\Controller\Index
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\DataObject $onestepData */
        $onestepData = $this->_getParamDataObject();

        if ($onestepData->getData('payment_method_data/method')) {
            $this->getOnePage()->savePayment($onestepData->getData('payment_method_data'));
        }

        if ($onestepData->getData('additional_data')) {
            $this->_checkoutSession->setAdditionalData($onestepData->getData('additional_data'));
        }

        $this->getOnePage()->getQuote()->collectTotals()->save();

        return $this->_getResultJson(
            $this->_systemConfig->getAjaxUpdateSectionConfig('address_review')
        );
    }
}