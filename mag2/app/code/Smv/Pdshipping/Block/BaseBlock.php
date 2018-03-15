<?php
/**
 * Copyright © 2015 Smv . All rights reserved.
 */
namespace Smv\Pdshipping\Block;

use Magento\Framework\UrlFactory;

class BaseBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Smv\Pdshipping\Helper\Data
     */
    protected $_devToolHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_urlApp;

    protected $_countryCollectionFactory;
    /**
     * @var \Smv\Pdshipping\Model\Config
     */
    protected $_config;
    protected $_scopeConfig;

    /**
     * @param \Smv\Pdshipping\Block\Context $context
     * @param \Magento\Framework\UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Smv\Pdshipping\Block\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        /**/
        $this->_devToolHelper = $context->getPdshippingHelper();
        $this->_config = $context->getConfig();
        $this->_urlApp = $context->getUrlFactory()->create();
        parent::__construct($context);

    }

    /**
     * Function for getting event details
     * @return array
     */
    public function getEventDetails()
    {
        return $this->_devToolHelper->getEventDetails();
    }
    /**
     * Function get dataConfig
     * @return string
     */
    public function dataConfig()
    {
        return $this->getDisplayShippingConfig();
    }

    /**
     * Function for getting current url
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlApp->getCurrentUrl();
    }

    /**
     * Function for getting controller url for given router path
     * @param string $routePath
     * @return string
     */
    public function getControllerUrl($routePath)
    {

        return $this->_urlApp->getUrl($routePath);
    }

    /**
     * Function for getting current url
     * @param string $path
     * @return string
     */
    public function getConfigValue($path)
    {
        return $this->_config->getCurrentStoreConfigValue($path);
    }

    /**
     * Function canShowPdshipping
     * @return bool
     */
    public function canShowPdshipping()
    {
        $isEnabled = $this->getConfigValue('pdshipping/module/is_enabled');
        if ($isEnabled) {
            $allowedIps = $this->getConfigValue('pdshipping/module/allowed_ip');
            if (is_null($allowedIps)) {
                return true;
            } else {
                $remoteIp = $_SERVER['REMOTE_ADDR'];
                if (strpos($allowedIps, $remoteIp) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    /*get all Countries*/
    protected function getTopDestinations()
    {
        $destinations = (string)$this->_scopeConfig->getValue(
            'general/country/destinations',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return !empty($destinations) ? explode(',', $destinations) : [];
    }

    public function getCountryCollection()
    {
        $collection = $this->_countryCollectionFactory->create()->loadByStore();

        return $collection;
    }

    public function getCountries()
    {
        $idCountry=$this->getSpeciFicCountry();
        $arrCountry=[];
        $options = $this->getCountryCollection()
            ->setForegroundCountries($this->getTopDestinations())
            ->toOptionArray();
        if ($this->getSallowSpeciFic()==1) {
            for ($j=0;$j<count($idCountry);$j++){
                for ($i=0;$i<count($options);$i++) {
                    if ($options[$i]['value']==$idCountry[$j]) {
                        $arrCountry[]=$options[$i];
                    }
                }
            }
            return $arrCountry;
        }
        return $options;
    }
    /*get All Country*/
    public function getAllCountry(){
        return $options = $this->getCountryCollection()
            ->setForegroundCountries($this->getTopDestinations())
            ->toOptionArray();
    }
    /*get specificcountry*/
    public function getSpeciFicCountry(){
        $speciFicCountry=$this->_scopeConfig->getValue('smvpdshipping/flatrate/specificcountry', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return explode(',', $speciFicCountry);
    }
    /*get specificcountry*/
    public function getSallowSpeciFic(){
        $sallowSpeciFic=$this->_scopeConfig->getValue('smvpdshipping/flatrate/sallowspecific', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $sallowSpeciFic;
    }
    /**********************/
    public function getDisplayShippingConfig(){
        $arr_config=[];
        $setting=$this->_scopeConfig->getValue('smvpdshipping/flatrate/shipping_setting_enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($setting!=null) {
            $arr_config['shipping_setting_enabled']=$setting;
        } else {
            $arr_config['shipping_setting_enabled']=1;
        }
        $region=$this->_scopeConfig->getValue('smvpdshipping/flatrate/shipping_setting_region', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($region!=null) {
            $arr_config['shipping_setting_region']=$region;
        } else {
            $arr_config['shipping_setting_region']=1;
        }
        $city=$this->_scopeConfig->getValue('smvpdshipping/flatrate/shipping_setting_city', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($city!=null) {
            $arr_config['shipping_setting_city']=$city;
        } else {
            $arr_config['shipping_setting_city']=0;
        }
        $postcode=$this->_scopeConfig->getValue('smvpdshipping/flatrate/shipping_setting_postcode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($postcode!=null) {
            $arr_config['shipping_setting_postcode']=$postcode;
        } else {
            $arr_config['shipping_setting_postcode']=1;
        }
        return $arr_config;
    }
}
