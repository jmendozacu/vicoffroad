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
 * Class Style
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Style extends \Magestore\OneStepCheckout\Block\AbstractOneStep
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_OneStepCheckout::style.phtml';

    /**
     * @return mixed|string
     */
    public function getStyleColor()
    {
        return $this->_systemConfig->getStyleColor();
    }

    /**
     * @return mixed|string
     */
    public function getButtonColor()
    {
        return $this->_systemConfig->getButtonColor();
    }

}