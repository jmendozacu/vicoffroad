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
 * Class FieldMapDataObject
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class FieldMapDataObject extends \Magento\Framework\DataObject
{
    const FIELD_FIRSTNAME = 'firstname';
    const FIELD_LASTNAME = 'lastname';
    const FIELD_EMAIL = 'email';
    const FIELD_TELEPHONE = 'telephone';
    const FIELD_STREET = 'street';
    const FIELD_COUNTRY_ID = 'country_id';
    const FIELD_REGION = 'region';
    const FIELD_REGION_ID = 'region_id';
    const FIELD_POSTCODE = 'postcode';
    const FIELD_CITY = 'city';
    const FIELD_COMPANY = 'company';
    const FIELD_FAX = 'fax';
    const FIELD_PREFIX = 'prefix';
    const FIELD_SUFFIX = 'suffix';
    const FIELD_MIDDLENAME = 'middlename';
    const FIELD_GENDER = 'gender';
    const FIELD_TAXVAT = 'vat_id';

    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Country
     */
    protected $_localCountry;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Customer\Block\Widget\Gender
     */
    protected $_genderWidget;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * FieldMapDataObject constructor.
     *
     * @param \Magento\Config\Model\Config\Source\Locale\Country $localCountry
     * @param \Magento\Framework\View\LayoutInterface            $layout
     * @param \Magento\Customer\Model\Customer                   $customer
     * @param array                                              $data
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Locale\Country $localCountry,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Customer\Model\Customer $customer,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->_localCountry = $localCountry;
        $this->_layout = $layout;
        $this->_customer = $customer;

        $this->_construct();
    }

    /**
     * @return $this
     */
    protected function _construct()
    {
        $this->_genderWidget = $this->_layout->createBlock('Magento\Customer\Block\Widget\Gender');
        $this->_initFieldMap();

        return $this;
    }

    /**
     * init field map
     */
    protected function _initFieldMap()
    {
        $this->_data = array_merge([
            'field_map'      => $this->getFieldMapData(),
            'address_fields' => $this->getAddressFieldsData(),
        ], $this->_data);
    }

    /**
     * @return array
     */
    public function getAddressFieldsData()
    {
        return [
            self::FIELD_FIRSTNAME,
            self::FIELD_LASTNAME,
            self::FIELD_EMAIL,
            self::FIELD_TELEPHONE,
            self::FIELD_STREET,
            self::FIELD_COUNTRY_ID,
            self::FIELD_POSTCODE,
            self::FIELD_REGION,
            self::FIELD_REGION_ID,
            self::FIELD_CITY,
            self::FIELD_COMPANY,
            self::FIELD_FAX,
            self::FIELD_PREFIX,
            self::FIELD_SUFFIX,
            self::FIELD_MIDDLENAME,
        ];
    }

    /**
     * @return array
     */
    public function getFieldMapData()
    {
        return [
            self::FIELD_FIRSTNAME  => [
                'label'    => __('First Name'),
                'title'    => __('First Name'),
                'type'     => 'text',
                'id_field' => self::FIELD_FIRSTNAME,
            ],
            self::FIELD_LASTNAME   => [
                'label'    => __('Last Name'),
                'title'    => __('Last Name'),
                'type'     => 'text',
                'id_field' => self::FIELD_LASTNAME,
            ],
            self::FIELD_EMAIL      => [
                'label'    => __('Email Address'),
                'title'    => __('Email Address'),
                'type'     => 'email',
                'id_field' => self::FIELD_EMAIL,
            ],
            self::FIELD_TELEPHONE  => [
                'label'    => __('Telephone'),
                'title'    => __('Telephone'),
                'type'     => 'text',
                'id_field' => self::FIELD_TELEPHONE,
            ],
            self::FIELD_STREET     => [
                'label'    => __('Street Address'),
                'title'    => __('Street Address'),
                'type'     => 'street',
                'id_field' => self::FIELD_STREET,
            ],
            self::FIELD_COUNTRY_ID => [
                'label'         => __('Country'),
                'title'         => __('Country'),
                'type'          => 'select',
                'header_option' => 'Please select country',
                'options'       => $this->_localCountry->toOptionArray(),
                'id_field'      => self::FIELD_COUNTRY_ID,
            ],
            self::FIELD_POSTCODE   => [
                'label'    => __('Postcode/Zipcode'),
                'title'    => __('Postcode/Zipcode'),
                'type'     => 'text',
                'id_field' => self::FIELD_POSTCODE,
            ],
            self::FIELD_REGION     => [
                'label'         => __('State/Province'),
                'title'         => __('State/Province'),
                'type'          => 'region',
                'header_option' => __('Please select region, state or province'),
                'id_field'      => self::FIELD_REGION,
            ],
            self::FIELD_CITY       => [
                'label'    => __('City'),
                'title'    => __('City'),
                'type'     => 'text',
                'id_field' => self::FIELD_CITY,
            ],
            self::FIELD_COMPANY    => [
                'label'    => __('Company'),
                'title'    => __('Company'),
                'type'     => 'text',
                'id_field' => self::FIELD_COMPANY,
            ],
            self::FIELD_FAX        => [
                'label'    => __('Fax'),
                'title'    => __('Fax'),
                'type'     => 'text',
                'id_field' => self::FIELD_FAX,
            ],
            self::FIELD_PREFIX     => [
                'label'    => __('Prefix Name'),
                'title'    => __('Prefix Name'),
                'type'     => 'text',
                'id_field' => self::FIELD_PREFIX,
            ],
            self::FIELD_SUFFIX     => [
                'label'    => __('Suffix Name'),
                'title'    => __('Suffix Name'),
                'type'     => 'text',
                'id_field' => self::FIELD_SUFFIX,
            ],
            self::FIELD_MIDDLENAME => [
                'label'    => __('Middle Name'),
                'title'    => __('Middle Name'),
                'type'     => 'text',
                'id_field' => self::FIELD_MIDDLENAME,
            ],
            self::FIELD_TAXVAT     => [
                'label'    => __('Tax/VAT number'),
                'title'    => __('Tax/VAT number'),
                'type'     => 'text',
                'id_field' => self::FIELD_TAXVAT,
            ],
            self::FIELD_GENDER     => [
                'label'         => __('Gender'),
                'title'         => __('Gender'),
                'type'          => 'select',
                'id_field'      => $this->_genderWidget->getFieldName('gender'),
                'header_option' => '',
                'options'       => $this->getGenderOption(),
            ],
        ];
    }

    /**
     * Get gender option.
     *
     * @return mixed
     */
    public function getGenderOption()
    {
        $genderOption = $this->_customer->getAttribute('gender')->getSource()->getAllOptions();
        array_shift($genderOption);

        return $genderOption;
    }
}
