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

namespace Magestore\OneStepCheckout\Block\OneStep\Address\Field;

/**
 * Class Region
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Region extends Select
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Magestore_OneStepCheckout::address/field/region.phtml';

    /**
     * @var string
     */
    protected $_optionValue = '';

    /**
     * @return mixed
     */
    public function getOptionValue()
    {
        return $this->_optionValue;
    }

    /**
     * @param mixed $optionValue
     *
     * @return Region
     */
    public function setOptionValue($optionValue)
    {
        $this->_optionValue = $optionValue;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->hasData('option_value')) {
            $this->getOptionValue($this->getData('option_value'));
        }
    }
}