<?php

namespace Smv\Ebaygallery\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /*General Settings*/
    
    const XML_PATH_GENERAL_ENABLE_MODULE           = 'photogallery/general/enable_module';
    const XML_PATH_GENERAL_PAGE_TITLE              = 'photogallery/photogallerysettings/page_title';
    const XML_PATH_GENERAL_SEO_URL_IDENTIFIER      = 'photogallery/photogallerysettings/seo_url_identifier';
    const XML_PATH_GENERAL_SEO_URL_SUFFIX          = 'photogallery/photogallerysettings/seo_url_suffix';
    const XML_PATH_GENERAL_SEO_META_KEYWORDS       = 'photogallery/photogallerysettings/meta_keywords';
    const XML_PATH_GENERAL_SEO_META_DESCRIPRION    = 'photogallery/photogallerysettings/meta_desp';
    
    /*Photo Gallery Settings*/
    
    const XML_PATH_PHOTOGALLERY_ENABLE_PRG         = 'photogallery/productsettings/enabled';
    const XML_PATH_PHOTOGALLERY_PAGINATION         = 'photogallery/photogallerysettings/images_per_page';
    const XML_PATH_PHOTOGALLERY_THUMB_WIDHT        = 'photogallery/imgsettings/thumb_width';
    const XML_PATH_PHOTOGALLERY_THUMB_HEIGHT       = 'photogallery/imgsettings/thumb_height';
    const XML_PATH_PHOTOGALLERY_BG_COLOR           = 'photogallery/imgsettings/bg_color';
    const XML_PATH_PHOTOGALLERY_BUTTON_TEXT        = 'photogallery/photogallerysettings/page_button_text';
    const XML_PATH_PHOTOGALLERY_FRAME_THUMB        = 'photogallery/imgsettings/frame_thumb';
    const XML_PATH_PHOTOGALLERY_ASPECT_RATIO       = 'photogallery/imgsettings/aspect_ration';
    const XML_PATH_PHOTOGALLERY_AUTO_PLAY          = 'photogallery/productsettings/autoplay';

    
    /*Category Settings*/

    const XML_PATH_PHOTOGALLERY_ENABLE_CAT = 'photogallery/catsettings/enabled';
    const XML_PATH_PHOTOGALLERY_CAT_POSITION = 'photogallery/catsettings/position';


    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    \Magento\Framework\ObjectManagerInterface $objectManager,
    \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
    $this->_objectManager = $objectManager;
    $this->_storeManager = $storeManager;
     parent::__construct($context); 
   }
    

    public function enableModule()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ENABLE_MODULE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
   
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getPageTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_PAGE_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSeoIdentifier()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SEO_URL_IDENTIFIER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSeoSuffix()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SEO_URL_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMetaKeywords()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SEO_META_KEYWORDS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SEO_META_DESCRIPRION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableProductRelatedGallery()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_ENABLE_PRG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableCatGallery()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_ENABLE_CAT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPagination()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_PAGINATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getThumbWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_THUMB_WIDHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getThumbHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_THUMB_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getBgcolor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_BG_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getKeepframe()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_FRAME_THUMB,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAspectratioflag()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_ASPECT_RATIO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getButtonText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_BUTTON_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPhotogalleryUrl()
    {
        $url = $this->getSeoIdentifier().$this->getSeoSuffix();
        return $this->_storeManager->getStore()->getUrl() . $url;
    }

    public  function getPhotogalleryPath()
    {
       $url = $this->getSeoIdentifier().$this->getSeoSuffix();
       return $url;
    }

    public function getAutoPlay()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_AUTO_PLAY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCatGalleryPosition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_CAT_POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    public function getThumbsDirPath($filePath = false)
    {
       $mediaRootDir = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).'photogallery/images/';
       $thumbnailDir = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).'photogallery/images/';
        
        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            
            $thumbnailDir .= dirname(substr($filePath, strlen($mediaRootDir)));
        }
        

        $thumbnailDir .=  '/'."thumb/";
        return $thumbnailDir;
    }

    public function getMediaUrl($url)
    {
        return $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).'photogallery/images/'.$url;
    }

    public function getJsUrl($url)
    {
        return $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).'photogallery/'.$url;
    }

}
