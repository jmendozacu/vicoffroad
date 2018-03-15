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

namespace Magestore\OneStepCheckout\Plugin\Checkout\Block\Checkout;

/**
 * class Link
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Tax extends \Magestore\OneStepCheckout\Plugin\AbstractPlugin
{

    public function afterGetTemplate(\Magento\Tax\Block\Checkout\Tax $taxBlock, $result)
    {
        if ((strtolower($this->getRequest()->getModuleName() == 'onestepcheckout'))||
            (strtolower($this->getRequest()->getModuleName() == 'giftvoucher'))) {
            return 'Magestore_OneStepCheckout::review/tax.phtml';
        } else {
            return $result;
        }

    }
}