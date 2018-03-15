<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbySeo\Helper;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Manager;

class Url extends AbstractHelper
{
    /** @var  Data */
    protected $helper;

    /** @var  Manager */
    protected $moduleManager;

    /** @var CategoryRepository  */
    protected $categoryRepository;

    protected $isBrandFilterActive;

    protected $originalParts;
    protected $config;
    protected $query;

    public function __construct(
        Context $context,
        Data $helper,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($context);
        $this->helper = $helper;
        $this->moduleManager = $context->getModuleManager();
        $this->categoryRepository = $categoryRepository;
    }

    public function seofyUrl($url)
    {
        if (!$this->initialize($url)) {
            return $url;
        }

        $this->query = $this->parseQuery();

        $routeUrl = $this->originalParts['route'];

        $moduleName = $this->_getRequest()->getModuleName();
        if (isset($this->query['cat']) && ($moduleName == 'catalog' || $moduleName == 'amshopby')) {
            $routeUrl = $this->followIntoCategory();
        }

        $endsWithLine = strlen($routeUrl) && $routeUrl[strlen($routeUrl) - 1] == '/';
        if ($endsWithLine) {
            return $url;
        }

        $routeUrlTrimmed = $this->removeCategorySuffix($routeUrl);
        $appendSuffix = $routeUrlTrimmed != $routeUrl;
        $resultPath = $routeUrlTrimmed;

        $seoAliases = $this->cutAliases();
        if ($seoAliases) {
            $resultPath = $this->injectAliases($resultPath, $seoAliases);
        }

        $resultPath = $this->cutReplaceExtraShopby($resultPath);
        $resultPath = ltrim($resultPath, '/');

        if ($appendSuffix) {
            $resultPath = $this->addCategorySuffix($resultPath);
        }

        $result = $this->query ? ($resultPath . '?' . $this->query2Params($this->query)) : $resultPath;
        $result .= $this->originalParts['hash'];

        return $this->originalParts['domain'] . $result;
    }

    protected function initialize($url)
    {
        $this->originalParts = [];
        $this->config = [];

        $this->config['brand_attribute_code'] = $this->moduleManager->isEnabled('Amasty_ShopbyBrand')
        && $this->scopeConfig->getValue('amshopby_brand/general/attribute_code')
            ? $this->scopeConfig->getValue('amshopby_brand/general/attribute_code') : null;

        $key = $this->scopeConfig->getValue('amshopby_root/general/url');
        $url = str_replace('amshopby/index/index/', $key, $url);

        if (!preg_match('@^([^/]*//[^/]*/)(.*)$@', $url, $globalParts)) {
            return false;
        }
        $this->originalParts['domain'] = $globalParts[1];

        $this->config['delimiter'] = strpos($url, '&amp;') === false ? '&' : '&amp;';

        $nativeParts = explode('?', $globalParts[2], 2);
        $this->originalParts['route'] = $nativeParts[0];

        $this->originalParts['params'] = null;
        $this->originalParts['hash'] = null;
        if (isset($nativeParts[1])) {
            $paramPart = $nativeParts[1];
            $hashPosition = strpos($paramPart, '#');
            if ($hashPosition !== false) {
                $hashPart = substr($paramPart, $hashPosition);
                $paramPart = substr($paramPart, 0, $hashPosition);
            } else {
                $hashPart = null;
            }
            $this->originalParts['params'] = $paramPart;
            $this->originalParts['hash'] = $hashPart;
        }

        return true;
    }

    protected function parseQuery()
    {
        $query = [];
        $this->isBrandFilterActive = false;

        if (!isset($this->originalParts['params'])) {
            return $query;
        }

        $parts = explode($this->config['delimiter'], $this->originalParts['params']);

        foreach ($parts as $part) {
            list($paramName, $value) = explode('=', $part, 2);
            $query[$paramName] = $value;

            if ($this->config['brand_attribute_code'] === $paramName) {
                $this->isBrandFilterActive = true;
            }
        }

        return $query;
    }

    protected function followIntoCategory()
    {
        $query = $this->query;
        $cat = (int) $query['cat'];
        unset($query['cat']);
        $category = $this->categoryRepository->get($cat);
        $categoryUrl = $category->getUrl();
        $this->query = $query;
        $routeUrl = substr($categoryUrl, strlen($this->originalParts['domain']));
        return $routeUrl;
    }

    protected function cutAliases()
    {
        $optionsData = $this->helper->getOptionsSeoData();

        $seoAliases = [];
        foreach ($this->query as $paramName => $rawValue) {
            if ($this->isParamSeoSignificant($paramName)) {
                $values = explode(',', str_replace('%2C', ',', $rawValue));
                foreach ($values as $value) {
                    if (!array_key_exists($value, $optionsData)) {
                        continue;
                    }
                    $alias = $optionsData[$value]['alias'];
                    $seoAliases[] = $alias;
                }
                unset($this->query[$paramName]);
            }
        }

        return $seoAliases;
    }

    protected function isParamSeoSignificant($param)
    {
        $seoList = $this->helper->getSeoSignificantUrlParameters();
        return in_array($param, $seoList);
    }

    protected function injectAliases($routeUrl, array $aliases)
    {
        $result = $routeUrl;
        if ($aliases) {
            $result .= '/' . implode('-', $aliases);
        }

        return $result;
    }

    protected function cutReplaceExtraShopby($url)
    {
        $cut = false;
        $allProductsEnabled = $this->moduleManager->isEnabled('Amasty_ShopbyRoot') && $this->scopeConfig->isSetFlag('amshopby_root/general/enabled');
        if ($allProductsEnabled || $this->moduleManager->isEnabled('Amasty_ShopbyBrand'))
        {
            $key = $this->scopeConfig->getValue('amshopby_root/general/url');
            $l = strlen($key);
            if (substr($url, 0, $l) == $key && strlen($url) > $l && $url[$l] != '?' && $url[$l] != '#') {
                $url = substr($url, strlen($key));
                $cut = true;
            }
        }

        if ($cut) {
            if ($this->isBrandFilterActive) {
                $key = trim($this->scopeConfig->getValue('amshopby_brand/general/url_key'));
                $url = $key . $url;
            }
        }
        return $url;
    }

    protected function query2Params($query)
    {
        $result = [];
        foreach ($query as $code => $value) {
            $result[] = $code . '=' . $value;
        }
        return implode($this->config['delimiter'], $result);
    }

    public function addCategorySuffix($url)
    {
        $suffix = $this->scopeConfig->getValue('catalog/seo/category_url_suffix');
        if (strlen($suffix)) {
            $url .= $suffix;
        }
        return $url;
    }

    public function removeCategorySuffix($url)
    {
        $suffix = $this->scopeConfig->getValue('catalog/seo/category_url_suffix');
        if (strlen($suffix)) {
            $p = strrpos($url, $suffix);
            if ($p !== false && $p == strlen($url) - strlen($suffix)) {
                $url = substr($url, 0, $p);
            }
        }
        return $url;
    }

    public function isSeoUrlEnabled()
    {
        return !!$this->scopeConfig->getValue('amasty_shopby_seo/url/mode');
    }
}
