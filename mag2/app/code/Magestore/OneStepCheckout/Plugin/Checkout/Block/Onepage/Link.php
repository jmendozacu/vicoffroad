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

namespace Magestore\OneStepCheckout\Plugin\Checkout\Block\Onepage;

/**
 * class Link
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Link extends \Magestore\OneStepCheckout\Plugin\AbstractPlugin
{
    /**
     * @param \Magento\Checkout\Block\Onepage\Link $onePageLink
     * @param                                      $result
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCheckoutUrl(\Magento\Checkout\Block\Onepage\Link $onePageLink, $result)
    {
        return $this->_getCheckoutUrl();
    }
}