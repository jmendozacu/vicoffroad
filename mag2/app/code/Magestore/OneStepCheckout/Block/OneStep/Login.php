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
 * class Login
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Login extends \Magestore\OneStepCheckout\Block\AbstractOneStep
{
    protected $_template = 'Magestore_OneStepCheckout::login_popup.phtml';

    public function enableRegistration()
    {
        return $this->_systemConfig->enableRegistration();
    }

    public function getComputeStyle()
    {
        $height = $this->_systemConfig->getTermHeight();
        if ($height) {
            $height = (int)$height - 87;
            $style = 'height: ' . ((int)$height) . 'px;';
        } else {
            $style = 'height: 446px;';
        }

        return $style;
    }

    /**
     * @return string
     */
    public function getAgreementStyle()
    {
        if ($this->_systemConfig->isEnableTermCustomSize()) {
            $width = $this->_systemConfig->getTermWidth() . 'px';
            $height = $this->_systemConfig->getTermHeight() . 'px';

            return 'width:' . $width . ';height:' . $height . ';';
        } else {
            return '';
        }
    }
}