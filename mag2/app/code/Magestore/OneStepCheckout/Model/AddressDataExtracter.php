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

namespace Magestore\OneStepCheckout\Model;

/**
 * class AddressDataExtracter
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class AddressDataExtracter
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var \Magestore\OneStepCheckout\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * AddressDataExtracter constructor.
     *
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param SystemConfig                         $systemConfig
     */
    public function __construct(
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magestore\OneStepCheckout\Model\SystemConfig $systemConfig
    )
    {
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_systemConfig = $systemConfig;
    }

    /**
     * @param \Magento\Framework\DataObject $addressData
     *
     * @return \Magento\Framework\DataObject
     */
    public function checkAddressData(\Magento\Framework\DataObject $addressData)
    {
        if (!$addressData->getData('country_id')) {
            $addressData->setData('country_id', $this->_systemConfig->getDefaultCountryId());
        }

        if (!$addressData->getData('postcode')) {
            $addressData->setData('postcode', $this->_systemConfig->getDefaultPostcode());
        }

        if (!$addressData->getData('region_id')) {
            $addressData->setData('region_id', $this->_systemConfig->getDefaultRegionId());
        }

        if ($addressData->hasData('vat_id')) {
            $addressData->setData('vat_id', trim($addressData->getData('vat_id')));
        }

        if ($addressData->hasData('email')) {
            $addressData->setData('email', trim($addressData->getData('email')));
        }

        return $addressData;
    }

    /**
     * @param \Magento\Framework\DataObject $onestepData
     *
     * @return mixed
     */
    public function extractShippingAddressData(
        \Magento\Framework\DataObject $dataObject,
        $shippingParam = 'shipping_address'
    )
    {
        /** @var \Magento\Framework\DataObject $shippingAddressObjectData */
        $shippingAddressObjectData = $this->_dataObjectFactory->create([
            'data' => is_array($dataObject->getData($shippingParam)) ? $dataObject->getData($shippingParam) : [],
        ]);

        $this->checkAddressData($shippingAddressObjectData);

        return $shippingAddressObjectData->getData();
    }

    /**
     * @param \Magento\Framework\DataObject $onestepData
     *
     * @return mixed
     */
    public function extractBillingAddressData(
        \Magento\Framework\DataObject $dataObject,
        $billingParam = 'billing_address'
    )
    {
        $shippingAddressData = $this->extractShippingAddressData($dataObject);

        /** @var \Magento\Framework\DataObject $billingAddressObjectData */
        $billingAddressObjectData = $this->_dataObjectFactory->create([
            'data' => is_array($dataObject->getData($billingParam)) ? $dataObject->getData($billingParam) : [],
        ]);

        if ($billingAddressObjectData->getData('use_same_shipping')) {
            $billingAddressObjectData->setData($shippingAddressData);
        } else {
            $this->checkAddressData($billingAddressObjectData);
        }

        return $billingAddressObjectData->getData();
    }
}