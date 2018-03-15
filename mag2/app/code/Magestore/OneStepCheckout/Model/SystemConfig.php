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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Session as customerSession;
use Magento\Framework\ObjectManagerInterface;
use Magestore\OneStepCheckout\Block\OneStep\Address\FieldMapDataObject;

/**
 * Class SystemConfig
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class SystemConfig extends \Magento\Framework\DataObject
{
    /**
     * Section config onestep checkout
     */
    const SECTION_CONFIG_ONESTEPCHECKOUT = 'onestepcheckout';

    const XML_GROUP_CONFIG_REQUIRE_FIELD = 'field_require_management';

    const XML_GROUP_CONFIG_AJAX_UPDATE = 'ajax_update';

    const XML_FIELD_POSITION_MANAGEMENT = 'onestepcheckout/field_position_management/row_';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var array
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_mapRequiredField = [
        FieldMapDataObject::FIELD_COMPANY,
        FieldMapDataObject::FIELD_STREET,
        FieldMapDataObject::FIELD_COUNTRY_ID,
        FieldMapDataObject::FIELD_REGION,
        FieldMapDataObject::FIELD_CITY,
        FieldMapDataObject::FIELD_POSTCODE,
        FieldMapDataObject::FIELD_TELEPHONE,
        FieldMapDataObject::FIELD_FAX,
        FieldMapDataObject::FIELD_PREFIX,
        FieldMapDataObject::FIELD_SUFFIX,
        FieldMapDataObject::FIELD_MIDDLENAME,
        FieldMapDataObject::FIELD_GENDER,
        FieldMapDataObject::FIELD_TAXVAT,
    ];

    /**
     * @var array
     */
    protected $_defaultRequiredFields = [
        FieldMapDataObject::FIELD_FIRSTNAME,
        FieldMapDataObject::FIELD_LASTNAME,
        FieldMapDataObject::FIELD_EMAIL,
    ];

    /**
     * @var array
     */
    protected $_ajaxUpdateFields = [
        'enable_ajax',
        'ajax_fields',
        'reload_section',
        'address_shipping',
        'address_payment',
        'address_review',
        'shipping_payment',
        'shipping_review',
        'payment_review',
    ];

    /**
     * @var customerSession
     */
    protected $_customerSession;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;

    /**
     * SystemConfig constructor.
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param customerSession       $customerSession
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        customerSession $customerSession,
        ObjectManagerInterface $objectManager,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->_resourceConfig = $resourceConfig;
    }

    /**
     * @param $relativePath
     *
     * @return mixed
     */
    protected function _getOneStepConfig($relativePath)
    {
        return $this->getConfig(self::SECTION_CONFIG_ONESTEPCHECKOUT . '/' . $relativePath);
    }

    /**
     * Get config by path.
     *
     * @param $path
     *
     * @return mixed
     */
    public function getConfig($path)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        return $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check module is Enabled.
     *
     * @return bool
     */
    public function isEnableOneStepCheckout()
    {
        return (boolean)$this->getGeneralConfig('active');
    }

    /**
     * Get carrier Name.
     *
     * @param $carrierCode
     *
     * @return mixed
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = $this->getConfig('carriers/' . $carrierCode . '/title')) {
            return $name;
        }

        return $carrierCode;
    }

    /**
     * Check is show login link
     *
     * @return mixed
     */
    public function isShowLoginLink()
    {
        return $this->_getOneStepConfig('checkout_mode/show_login_link');
    }

    /**
     * Check is enable registration when checkout
     *
     * @return mixed
     */
    public function isEnableRegistration()
    {
        return $this->_getOneStepConfig('checkout_mode/enable_registration');
    }

    /**
     * Check allow guest customer checkout
     *
     * @return mixed
     */
    public function isAllowGuest()
    {
        return $this->_getOneStepConfig('checkout_mode/allow_guest');
    }

    /**
     * Get login link title
     *
     * @return mixed
     */
    public function getLoginLinkTitle()
    {
        return $this->_getOneStepConfig('checkout_mode/login_link_title');
    }

    /**
     * Check Enable Terms and Conditions
     *
     * @return mixed
     */
    public function isEnabledProductImage()
    {
        return $this->_getOneStepConfig('general/enable_items_image');
    }

    /**
     * @return mixed
     */
    public function isEnabledAgreements()
    {
        return $this->_getOneStepConfig('terms_conditions/enable_terms');
    }

    /**
     * @return mixed
     */
    public function getTermTitle()
    {
        return $this->_getOneStepConfig('terms_conditions/term_title');
    }

    /**
     * @return mixed
     */
    public function getTermHtml()
    {
        return $this->_getOneStepConfig('terms_conditions/term_html');
    }

    /**
     * @return mixed
     */
    public function isEnableTermCustomSize()
    {
        return $this->_getOneStepConfig('terms_conditions/enable_custom_size');
    }

    /**
     * @return mixed
     */
    public function getTermWidth()
    {
        return $this->_getOneStepConfig('terms_conditions/term_width');
    }

    /**
     * @return mixed
     */
    public function getTermHeight()
    {
        return $this->_getOneStepConfig('terms_conditions/term_height');
    }

    /**
     * @return mixed
     */
    public function getEmailTemplate()
    {
        return $this->_getOneStepConfig('order_notification/template');
    }

    /**
     * @return mixed
     */
    public function isEnableSendEmailAdmin()
    {
        return $this->_getOneStepConfig('order_notification/enable_notification');
    }

    /**
     * @return mixed
     */
    public function notifyToEmail()
    {
        return $this->_getOneStepConfig('order_notification/notification_email');
    }

    /**
     * @return mixed
     */
    public function enableSurvey()
    {
        return $this->_getOneStepConfig('survey/enable_survey');
    }

    /**
     * @return mixed
     */
    public function getSurveyQuestion()
    {
        return $this->_getOneStepConfig('survey/survey_question');
    }

    /**
     * @return mixed
     */
    public function enableFreeText()
    {
        return $this->_getOneStepConfig('survey/enable_survey_freetext');
    }

    /**
     * @return mixed
     */
    public function getSurveyValues()
    {
        return $this->_getOneStepConfig('survey/survey_values');
    }

    /**
     * @return bool
     */
    public function enableGiftMessage()
    {
        return $this->_getOneStepConfig('giftmessage/enable_giftmessage');
    }

    /**
     * @return mixed
     */
    public function isShowNewsletter()
    {
        return $this->getGeneralConfig('show_newsletter');
    }

    /**
     * @return mixed
     */
    public function isSubscribeByDefault()
    {
        return $this->getGeneralConfig('newsletter_default_checked');
    }

    /**
     * @return mixed
     */
    public function isShowComment()
    {
        return $this->getGeneralConfig('show_comment');
    }

    /**
     * @return mixed
     */
    public function isShowTermCondition()
    {
        return $this->_getOneStepConfig('terms_conditions/enable_terms');
    }

    /**
     * @return mixed
     */
    public function getOneStepDescription()
    {
        return $this->getGeneralConfig('checkout_description');
    }

    /**
     * @return mixed
     */
    public function getOneStepTitle()
    {
        return $this->getGeneralConfig('checkout_title');
    }

    /**
     * @return mixed
     */
    public function isShowDiscount()
    {
        return $this->getGeneralConfig('show_discount');
    }

    /**
     * @return mixed
     */
    public function isShowDelivery()
    {
        return $this->getGeneralConfig('delivery_time_date');
    }

    /**
     * @return bool
     */
    public function canShowLoginLink()
    {
        if (!$this->isCustomerLogin() && $this->isShowLoginLink()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return bool
     */
    public function isCustomerLogin()
    {
        return $this->_customerSession->isLoggedIn();
    }


    /**
     * Get default country id
     *
     * @return mixed
     */
    public function getDefaultCountryId()
    {
        return $this->getGeneralConfig('country_id');
    }

    /**
     * Get default Postcode
     *
     * @return mixed
     */
    public function getDefaultPostcode()
    {
        return $this->getGeneralConfig('postcode');
    }

    /**
     * Get default Region Id
     *
     * @return mixed
     */
    public function getDefaultRegionId()
    {
        return $this->getGeneralConfig('region_id');
    }

    /**
     * Get default city name
     *
     * @return mixed
     */
    public function getDefaultCity()
    {
        return $this->getGeneralConfig('city');
    }

    /**
     * @param $fieldId
     *
     * @return mixed
     */
    public function getRequiredField($field)
    {
        return $this->_getOneStepConfig(self::XML_GROUP_CONFIG_REQUIRE_FIELD . '/' . $field);
    }

    /**
     * Get ajax update section config
     *
     * @param $field
     *
     * @return mixed
     */
    public function getAjaxUpdateSectionConfig($field = '')
    {
        return $this->_getOneStepConfig(self::XML_GROUP_CONFIG_AJAX_UPDATE . '/' . $field);
    }

    /**
     * @return mixed
     */
    public function isAjaxUpdateOnChangeAddress()
    {
        return (boolean)$this->getAjaxUpdateSectionConfig('enable_ajax');
    }

    /**
     * @return mixed
     */
    public function getTriggeringFieldsChange()
    {
        $triggerFieldsChange = explode(',', $this->getAjaxUpdateSectionConfig('ajax_fields'));

        if (in_array(FieldMapDataObject::FIELD_REGION, $triggerFieldsChange)) {
            $triggerFieldsChange[] = FieldMapDataObject::FIELD_REGION_ID;
        }

        return $triggerFieldsChange;
    }

    /**
     * @return mixed
     */
    public function getReloadSectionType()
    {
        return $this->getAjaxUpdateSectionConfig('reload_section');
    }

    /**
     * @return array
     */
    public function getSectionUpdateOnChangeAddress()
    {
        return [
            'shippingMethod' => (boolean)$this->getAjaxUpdateSectionConfig('address_shipping'),
            'payment'        => (boolean)$this->getAjaxUpdateSectionConfig('address_payment'),
            'review'         => (boolean)$this->getAjaxUpdateSectionConfig('address_review'),
        ];
    }

    /**
     * @return array
     */
    public function getSectionUpdateOnChangeShippingMehtod()
    {
        return [
            'payment' => (boolean)$this->getAjaxUpdateSectionConfig('shipping_payment'),
            'review'  => (boolean)$this->getAjaxUpdateSectionConfig('shipping_review'),
        ];
    }

    /**
     * @return array
     */
    public function getSectionUpdateOnChangePaymentMehtod()
    {
        return [
            'review' => (boolean)$this->getAjaxUpdateSectionConfig('payment_review'),
        ];
    }

    /**
     * @return array
     */
    public function getListReqiredFields()
    {
        if (!$this->hasData('list_required_fields')) {
            $listRequiredFields = [];
            foreach ($this->_mapRequiredField as $fieldId) {
                if ($this->getRequiredField($fieldId)) {
                    $listRequiredFields[] = $fieldId;
                }
            }
            $listRequiredFields = array_merge($listRequiredFields, $this->_defaultRequiredFields);
            $this->setData('list_required_fields', $listRequiredFields);
        }

        return $this->getData('list_required_fields');
    }

    /**
     * Get field matrix option
     *
     * example: [ ['firstname', 'lastname'], ['telephone'], ..]
     *
     * @return mixed
     */
    public function getFieldsMatrixOption()
    {
        if (!$this->hasData('fields_matrix')) {
            $fieldMatrix = [];
            for ($index = 0; $index <= 10; ++$index) {
                $left = $this->getConfig(self::XML_FIELD_POSITION_MANAGEMENT . (2 * $index));
                $right = $this->getConfig(self::XML_FIELD_POSITION_MANAGEMENT . (2 * $index + 1));
                $row = [];

                if ($left) {
                    $row[] = $left;
                }

                if ($right) {
                    $row[] = $right;
                }

                if (!empty($row)) {
                    $fieldMatrix[] = $row;
                }
            }

            $this->setData('fields_matrix', $fieldMatrix);
        }

        return $this->getData('fields_matrix');
    }

    /**
     * Get general config
     *
     * @param $field
     *
     * @return mixed
     */
    public function getGeneralConfig($field)
    {
        return $this->_getOneStepConfig('general/' . $field);
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultShippingMethod()
    {
        return $this->getGeneralConfig('default_shipping');
    }

    /**
     * @return mixed
     */
    public function getDefaultPaymentMethod()
    {
        return $this->getGeneralConfig('default_payment');
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    public function _getCheckoutModeConfig($field)
    {
        return $this->_getOneStepConfig('checkout_mode/' . $field);
    }


    /**
     * @return mixed
     */
    public function enableRegistration()
    {
        return (boolean)$this->_getCheckoutModeConfig('enable_registration');
    }

    /**
     * @return bool
     */
    public function allowGuestCheckout()
    {
        return (boolean)$this->_getCheckoutModeConfig('allow_guest');
    }

    /**
     * @return bool
     */
    public function allowRedirectCheckoutAfterAddProduct()
    {
        return (boolean)$this->getGeneralConfig('redirect_to_checkout');
    }

    /**
     * @return bool
     */
    public function allowSuggestingAddress()
    {
        return (boolean)$this->getGeneralConfig('suggest_address');
    }

    /**
     * @return bool
     */
    public function enableBillingDifferentAddress()
    {
        return (boolean)$this->getGeneralConfig('show_shipping_address');
    }

    /**
     * @return mixed|string
     */
    public function getStyleColor()
    {
        $style = $this->_getOneStepConfig('style_management/style');
        $colorStyle = $this->_getOneStepConfig('style_management/custom_style');
        if ($style == 'custom') {
            return '#' . $colorStyle;
        } else {
            return $style;
        }
    }

    /**
     * @return mixed|string
     */
    public function getButtonColor()
    {
        $button = $this->_getOneStepConfig('style_management/button');
        $buttonStyle = $this->_getOneStepConfig('style_management/custom_button');
        if ($button == 'custom') {
            return '#' . $buttonStyle;
        } else {
            return $button;
        }
    }

    public function isEnableGiftWrap()
    {
        return $this->_getOneStepConfig('giftwrap/enable_giftwrap');
    }

    public function getGiftWrapType()
    {
        return $this->_getOneStepConfig('giftwrap/giftwrap_type');
    }

    public function getGiftWrapAmount()
    {
        return $this->_getOneStepConfig('giftwrap/giftwrap_amount');
    }

    public function getResourceConfig()
    {
        return $this->_resourceConfig;
    }

    /**
     * Hide section Shipping Method if only one method is applicable
     *
     * @return bool
     */
    public function hideOneShippingMethod()
    {
        return (boolean)$this->getGeneralConfig('hide_shipping_method');
    }
}

