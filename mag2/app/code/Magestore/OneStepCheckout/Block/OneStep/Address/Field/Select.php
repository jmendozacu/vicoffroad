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
 * Class Select
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Select extends Text
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Magestore_OneStepCheckout::address/field/select.phtml';

    /**
     * @var array
     */
    protected $_options = [];

    /**
     * @var string
     */
    protected $_headerOption = 'Please select an option';

    /**
     * @param array $options
     *
     * @return Select
     */
    public function setOptions($options)
    {
        $this->_options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param string $headerOption
     *
     * @return Select
     */
    public function setHeaderOption($headerOption)
    {
        $this->_headerOption = $headerOption;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderOption()
    {
        return $this->_headerOption;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->hasData('options')) {
            $this->setOptions($this->getData('options'));
        }

        if ($this->hasData('header_option')) {
            $this->setHeaderOption($this->getData('header_option'));
        }
    }
}