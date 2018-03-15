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
 * Class Row
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Row extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_OneStepCheckout::address/row.phtml';

    /**
     * fields data
     *
     * @var array
     */
    protected $_fields = [];

    /**
     * @var FieldFactory
     */
    protected $_fieldFactory;

    /**
     * Row constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param FieldFactory                                     $fieldFactory
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\OneStepCheckout\Block\OneStep\Address\FieldFactory $fieldFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_fieldFactory = $fieldFactory;

    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->hasData('fields')) {
            $this->setFields($this->getData('fields'));
        }
    }


    /**
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @param array $fields
     *
     * @return Field
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $numberField = 0;

        foreach ($this->getFields() as $idField => $fieldData) {
            /** @var \Magestore\OneStepCheckout\Block\OneStep\Address\AbstractField $fieldBlock */
            if (!empty($fieldData)) {
                $fieldBlock = $this->_fieldFactory->createField(
                    $fieldData['type'],
                    $this->getNameInLayout() . '_' . $idField,
                    [
                        'data' => $fieldData,
                    ]
                );
            }

            if (++$numberField == $this->getCountFields()) {
                $fieldBlock->setLast(TRUE);
            }

            $this->setChild($idField, $fieldBlock);
        }

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getRowTypeClass()
    {
        if (!$this->hasData('row_type_class')) {
            if ($this->hasOneField()) {
                $this->setData('row_type_class', 'one-field');
            } elseif ($this->hasTwoFields()) {
                $this->setData('row_type_class', 'two-fields');
            }
        }

        return $this->getData('row_type_class');
    }

    /**
     * @return \Magestore\OneStepCheckout\Block\OneStep\Address\Field\Text[]
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildBlocks()
    {
        return $this->getLayout()->getChildBlocks($this->getNameInLayout());
    }

    /**
     * @param $idField
     *
     * @return string
     */
    public function getWrapperClass(\Magestore\OneStepCheckout\Block\OneStep\Address\AbstractField $fieldBlock)
    {
        $wrapperClass = $this->getRowTypeClass();
        $wrapperClass .= $this->isSelectType($fieldBlock) ? ' mdl-selectfield' : '';

        if ($fieldBlock->isLast()) {
            $wrapperClass .= ' last';
        }

        return $wrapperClass;
    }

    /**
     * @return bool
     */
    public function hasOneField()
    {
        return $this->getCountFields() == 1;
    }

    /**
     * @return bool
     */
    public function hasTwoFields()
    {
        return $this->getCountFields() == 2;
    }

    /**
     * Get num
     *
     * @return int
     */
    public function getCountFields()
    {
        return count($this->_fields);
    }


    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     *
     * @return bool
     */
    public function isSelectType($block)
    {
        return $block instanceof \Magestore\OneStepCheckout\Block\OneStep\Address\Field\Select;
    }
}