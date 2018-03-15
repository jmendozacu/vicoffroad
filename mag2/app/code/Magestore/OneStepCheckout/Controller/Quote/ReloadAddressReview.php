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

namespace Magestore\OneStepCheckout\Controller\Quote;

/**
 * Class EditQty
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class ReloadAddressReview extends \Magestore\OneStepCheckout\Controller\Index
{
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = [];
        $isVirtual = $this->getQuote()->isVirtual();
        $quoteQty = $this->getQuote()->getItemsQty();

        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->addHandle('onestepcheckout_handle_ajax_update');
        $result['review_info'] = $resultLayout->getLayout()->getBlock("review_info")->toHtml();
        $result['shipping_method'] = $resultLayout->getLayout()->getBlock('onestepcheckout_shipping_method_available')->toHtml();
        $result['payment_method'] = true;
        $result['giftWrap_amount'] = $this->_objectManager->get('Magento\Checkout\Helper\Data')->formatPrice($this->_oscHelper->getOrderGiftWrapAmount());

        if (!(int)$quoteQty) {
            $result['empty_quote'] = TRUE;
        }
        if ($isVirtual) {
            $result['is_virtual'] = TRUE;
        }

        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
