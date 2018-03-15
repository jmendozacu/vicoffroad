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
 * Class FieldFactory
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class FieldFactory
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var array
     */
    protected $_typeMap = [
        'text'   => 'Magestore\OneStepCheckout\Block\OneStep\Address\Field\Text',
        'street' => 'Magestore\OneStepCheckout\Block\OneStep\Address\Field\Street',
        'select' => 'Magestore\OneStepCheckout\Block\OneStep\Address\Field\Select',
        'region' => 'Magestore\OneStepCheckout\Block\OneStep\Address\Field\Region',
        'email'  => 'Magestore\OneStepCheckout\Block\OneStep\Address\Field\Email',
    ];

    /**
     * FieldFactory constructor.
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(\Magento\Framework\View\LayoutInterface $layout)
    {
        $this->_layout = $layout;
    }

    /**
     * @param        $type
     * @param string $name
     * @param array  $arguments
     *
     * @return \Magento\Framework\View\Element\BlockInterface|null
     */
    public function createField($type, $name = '', $arguments = [])
    {
        if (array_key_exists($type, $this->_typeMap)) {
            return $this->_layout->createBlock($this->_typeMap[$type], $name, $arguments);
        }

        return NULL;
    }
}