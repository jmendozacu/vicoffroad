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

use Magestore\OneStepCheckout\Block\OneStep\Address\FieldMapDataObject;

/**
 * class AbstractAddress
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
abstract class AbstractAddress extends \Magestore\OneStepCheckout\Block\AbstractOneStep
{
    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Country
     */
    protected $_localCountry;

    /**
     * @var string
     */
    protected $_htmlPrefix = '';

    /**
     * @var FieldMapDataObject
     */
    protected $_fieldMapDataObject;

    /**
     * @var FieldFactory
     */
    protected $_fieldFactory;

    /**
     * AbstractAddress constructor.
     *
     * @param \Magestore\OneStepCheckout\Block\Context           $context
     * @param FieldMapDataObject                                 $fieldMapDataObject
     * @param Address\FieldFactory                               $fieldFactory
     * @param \Magento\Config\Model\Config\Source\Locale\Country $localCountry
     * @param \Magento\Framework\Logger\Monolog                  $monolog
     * @param array                                              $data
     */
    public function __construct(
        \Magestore\OneStepCheckout\Block\Context $context,
        FieldMapDataObject $fieldMapDataObject,
        \Magestore\OneStepCheckout\Block\OneStep\Address\FieldFactory $fieldFactory,
        \Magento\Config\Model\Config\Source\Locale\Country $localCountry,
        \Magento\Framework\Logger\Monolog $monolog,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_localCountry = $localCountry;
        $this->_fieldMapDataObject = $fieldMapDataObject;
        $this->_fieldFactory = $fieldFactory;
    }

    /**
     * @return string
     */
    public function getHtmlPrefix()
    {
        return $this->_htmlPrefix;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        foreach ($this->getFieldsMatrix() as $index => $row) {
            $this->_prepareRow($row, $index);
        }

        return parent::_prepareLayout();
    }

    /**
     * Revmove a field from fields matrix
     *
     * @param $fieldsMatrix
     * @param $field
     */
    protected function removeField(&$fieldsMatrix, $field)
    {
        foreach ($fieldsMatrix as &$row) {
            $key = array_search($field, $row);
            if ($key !== FALSE) {
                unset($row[$key]);
                $row = array_values($row);
            }

        }
    }

    /**
     * @param array $row
     * @param       $index
     */
    protected function _prepareRow(array $row, $index)
    {
        $fields = [];
        foreach ($row as $field) {
            $fields[$field] = $this->getFieldData($field);
        }

        $this->addChild($this->getNameInLayout() . '_row_' . $index,
            'Magestore\OneStepCheckout\Block\OneStep\Address\Row',
            [
                'fields' => $fields,
            ]
        );
    }

    /**
     * @param $idField
     *
     * @return array|mixed
     */
    public function getFieldData($idField)
    {
        $requireFields = $this->getRequireFields();

        if ($this->_fieldMapDataObject->getData('field_map/' . $idField)) {
            $fieldData = $this->_fieldMapDataObject->getData('field_map/' . $idField);

            if (in_array($idField, $requireFields)) {
                $fieldData['required'] = TRUE;
            }

            $fieldData['html_prefix'] = $this->getHtmlPrefix();

            $fieldData['value'] = $this->_getDefaultValue($idField);

            if($idField == 'region') {
                $fieldData['option_value'] = $this->_getDefaultValue('region_id');
            }

            return $fieldData;
        }

        return [];
    }

    /**
     * Get default value for field
     *
     * @param $idField
     *
     * @return mixed
     */
    protected function _getDefaultValue($idField)
    {
        if (($value = $this->getQuoteAddress()->getData($idField))
            || ($value = $this->getCustomer()->getData($idField))
            || ($value = $this->_systemConfig->getGeneralConfig($idField))
        ) {

            return $value;
        }
    }

    /**
     * Get maxtrix fields
     *
     * @return mixed
     */
    abstract public function getFieldsMatrix();

    /**
     * Get current quote address
     *
     * @return mixed
     */
    abstract public function getQuoteAddress();

    /**
     * Check can show password section
     *
     * @return bool
     */
    public function canShowPasswordSection()
    {
        return !$this->isCustomerLoggedIn()
        && $this->allowGuestCheckout()
        && $this->enableRegistration();
    }

    /**
     * Get option country
     *
     * @return array
     */
    public function getOptionCountry()
    {
        return $this->_localCountry->toOptionArray();
    }

    /**
     * @param $type
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = [];
            foreach ($this->getCustomerAddresses() as $address) {
                $options[] = [
                    'value' => $address->getId(),
                    'label' => $address->format('oneline'),
                ];
            }
            $addressId = $this->getAddress()->getId();
            $shippingAddressId = $this->getCustomer()->getDefaultShipping();

            if ($shippingAddressId != $addressId && $type == 'shipping') {
                $addressId = $shippingAddressId;
            }

            if (empty($addressId)) {
                if ($type == 'billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            /** @var \Magento\Framework\View\Element\Html\Select $select */
            $select = $this->getLayout()->createBlock('\Magento\Framework\View\Element\Html\Select')
                ->setName($type . '_address_id')
                ->setId($type . '-address-select')
                ->setClass('address-select')
                ->setExtraParams('style="width:350px"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', __('New Address'));

            return $select->getHtml();
        }

        return '';
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address|mixed
     */
    public function getAddress()
    {
        if ($this->isCustomerLoggedIn()) {
            $customerAddressId = $this->getCustomer()->getDefaultBilling();
            if ($customerAddressId) {
                $billing = $this->_objectManager->create('Magento\Customer\Model\Address')->load($customerAddressId);
            } else {
                $billing = $this->getQuote()->getBillingAddress();
            }

            if (!$billing->getCustomerAddressId()) {
                $customer = $this->getCustomer();
                $defaultBillingAddress = $customer->getDefaultBillingAddress();

                if ($defaultBillingAddress && $defaultBillingAddress->getId()) {
                    foreach ($this->_fieldMapDataObject->getData('address_fields') as $addressField) {
                        if ($defaultBillingAddress->getData($addressField)) {
                            $billing->setData($addressField, $defaultBillingAddress->getData($addressField));
                        }
                    }
                    $billing->setCustomerAddressId($defaultBillingAddress->getId())->save();
                } else {
                    return $billing;
                }
            }

            return $billing;
        } else {
            return $this->_objectManager->get('Magento\Quote\Model\Quote\Address');
        }
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getCustomerAddresses()
    {
        return $this->getCustomer()->getAddresses();
    }

    /**
     * @return int
     */
    public function customerHasAddresses()
    {
        return count($this->getCustomer()->getAddresses());
    }
}