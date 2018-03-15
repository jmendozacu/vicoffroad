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

use Magento\Framework\App\ResponseInterface;

/**
 * class SaveMethod
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class SaveShippingMethod extends \Magestore\OneStepCheckout\Controller\Index
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

        if ($onestepData->getData('shipping_method')) {
            $this->getOnePage()->saveShippingMethod($onestepData->getData('shipping_method'));
        }

        $this->getOnePage()->getQuote()->collectTotals()->save();

        return $this->_getResultJson(
            $this->_systemConfig->getAjaxUpdateSectionConfig('address_review'),
            $this->_systemConfig->getAjaxUpdateSectionConfig('address_payment')
        );
    }
}