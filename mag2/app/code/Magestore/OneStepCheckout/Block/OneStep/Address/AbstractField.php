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

namespace Magestore\OneStepCheckout\Block\OneStep\Address;

/**
 * Class AbstractField
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class AbstractField extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_fieldItemClass = 'field-item';

    /**
     * @var string
     */
    protected $_htmlPrefix = '';

    /**
     * @var string
     */
    protected $_label = '';

    /**
     * @var string
     */
    protected $_title = '';

    /**
     * @var string
     */
    protected $_idField = '';

    /**
     * @var bool
     */
    protected $_required = FALSE;

    /**
     * @var string
     */
    protected $_value = '';

    /**
     * @var string
     */
    protected $_style = '';

    /**
     * Check is last field
     * @var bool
     */
    protected $_last = FALSE;

    /**
     * Text constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->hasData('field_item_class')) {
            $this->setFieldItemClass($this->getData('field_item_class'));
        }

        if ($this->hasData('label')) {
            $this->setLabel($this->getData('label'));
        }

        if ($this->hasData('title')) {
            $this->setTitle($this->getData('title'));
        }

        if ($this->hasData('id_field')) {
            $this->setIdField($this->getData('id_field'));
        }

        if ($this->hasData('required')) {
            $this->setRequired($this->getData('required'));
        }

        if ($this->hasData('html_prefix')) {
            $this->setHtmlPrefix($this->getData('html_prefix'));
        }

        if ($this->hasData('last')) {
            $this->setLast($this->getData('last'));
        }

        if ($this->hasData('value')) {
            $this->setValue($this->getData('value'));
        }

        if ($this->hasData('style')) {
            $this->setStyle($this->getData('style'));
        }
    }

    /**
     * @param array $label
     *
     * @return Field
     */
    public function setLabel($label)
    {
        $this->_label = $label;

        return $this;
    }

    /**
     * @return array
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @param string $title
     *
     * @return Field
     */
    public function setTitle($title)
    {
        $this->_title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param mixed $idField
     *
     * @return Field
     */
    public function setIdField($idField)
    {
        $this->_idField = $idField;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdField()
    {
        return $this->_idField;
    }

    /**
     * @param boolean $required
     *
     * @return Field
     */
    public function setRequired($required)
    {
        $this->_required = $required;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->_required;
    }

    /**
     * @param mixed $htmlPrefix
     *
     * @return Field
     */
    public function setHtmlPrefix($htmlPrefix)
    {
        $this->_htmlPrefix = $htmlPrefix;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHtmlPrefix()
    {
        return $this->_htmlPrefix;
    }

    /**
     * @param string $style
     *
     * @return Text
     */
    public function setStyle($style)
    {
        $this->_style = $style;

        return $this;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->_style;
    }

    /**
     * @param string $value
     *
     * @return Text
     */
    public function setValue($value)
    {
        $this->_value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @param boolean $isLastField
     *
     * @return AbstractField
     */
    public function setLast($isLastField)
    {
        $this->_last = $isLastField;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isLast()
    {
        return $this->_last;
    }

    /**
     * @return string
     */
    public function getHtmlName()
    {
        return sprintf('%s[%s]', $this->getHtmlPrefix(), $this->getIdField());
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return sprintf('%s:%s', $this->getHtmlPrefix(), $this->getIdField());
    }

    /**
     * @param string $fieldClass
     *
     * @return AbstractField
     */
    public function setFieldItemClass($fieldClass)
    {
        $this->_fieldItemClass = $fieldClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldItemClass()
    {
        return $this->_fieldItemClass;
    }
}