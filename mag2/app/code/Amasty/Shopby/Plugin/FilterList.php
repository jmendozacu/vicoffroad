<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Shopby\Plugin;

use Amasty\Shopby\Model\Source\VisibleInCategory;

class FilterList
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var \Amasty\Shopby\Helper\FilterSetting\Proxy  */
    protected $filterSetting;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Amasty\Shopby\Helper\FilterSetting\Proxy $filterSetting
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\Shopby\Helper\FilterSetting\Proxy $filterSetting
    ){
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
        $this->filterSetting = $filterSetting;

    }

    /**
     * @param \Magento\Catalog\Model\Layer\FilterList $subject
     * @param \Closure                                $closure
     * @param \Magento\Catalog\Model\Layer            $layer
     *
     * @return array
     */
    public function aroundGetFilters(\Magento\Catalog\Model\Layer\FilterList $subject, \Closure $closure, \Magento\Catalog\Model\Layer $layer)
    {
        $listFilters = $this->matchFilters($closure($layer), $layer);
        $listAdditionalFilters = $this->getAdditionalFilters($layer);
        return $this->insertAdditionalFilters($listFilters, $listAdditionalFilters);
    }

    /**
     * @param array $listFilters
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function matchFilters(array $listFilters, \Magento\Catalog\Model\Layer $layer)
    {
        $matchedFilters = [];
        foreach($listFilters as $idx => $filter){
            $setting = $this->filterSetting->getSettingByLayerFilter($filter);

            if ($setting->getVisibleInCategories() === VisibleInCategory::ONLY_IN_SELECTED_CATEGORIES &&
                !in_array($layer->getCurrentCategory()->getId(), $setting->getCategoriesFilter())
            ){
                continue;
            } else if ($setting->getVisibleInCategories() === VisibleInCategory::HIDE_IN_SELECTED_CATEGORIES &&
                in_array($layer->getCurrentCategory()->getId(), $setting->getCategoriesFilter())
            ){
                continue;
            } else if (($attributesFilter = $setting->getAttributesFilter()) &&
                count($attributesFilter) > 0 &&
                $layer->getState()->getData('filters') !== null
            ){
                $stateAttributes = $this->getStateAttributesIds($layer);
                $intersects = array_intersect($attributesFilter, $stateAttributes);
                if (count($intersects) === 0){
                    continue;
                }
            } else if (($attributesOptionsFilter = $setting->getAttributesOptionsFilter()) &&
                count($attributesOptionsFilter) > 0 &&
                $layer->getState()->getData('filters') !== null
            ){
                $stateAttributesOptions = $this->getStateAttributesOptionsFilter($layer);
                $intersects = array_intersect($attributesOptionsFilter, $stateAttributesOptions);
                if (count($intersects) === 0){
                    continue;
                }
            }

            $matchedFilters[] = $filter;
        }

        return $matchedFilters;
    }

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getStateAttributesIds(\Magento\Catalog\Model\Layer $layer)
    {
        $ids = [];
        foreach ($layer->getState()->getFilters() as $filter) {
            $ids[] = $filter->getFilter()->getAttributeModel()->getId();
        }
        return array_unique($ids);
    }

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getStateAttributesOptionsFilter(\Magento\Catalog\Model\Layer $layer)
    {
        $ids = [];
        foreach ($layer->getState()->getFilters() as $filter) {
            $ids[] = $filter->getValueString();
        }
        return array_unique($ids);
    }

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     *
     * @return array
     */
    protected function getAdditionalFilters(\Magento\Catalog\Model\Layer $layer)
    {
        if(is_null($this->filters)) {
            $this->filters = [];
            $isStockEnabled = $this->scopeConfig->isSetFlag('amshopby/stock_filter/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if($isStockEnabled && $this->isEnabledShowOutOfStock()) {
                $this->filters[] = $this->objectManager->create('Amasty\Shopby\Model\Layer\Filter\Stock', ['layer'=>$layer]);
            }
            $isRatingEnabled = $this->scopeConfig->isSetFlag('amshopby/rating_filter/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if($isRatingEnabled) {
                $this->filters[] = $this->objectManager->create('Amasty\Shopby\Model\Layer\Filter\Rating', ['layer'=>$layer]);
            }
        }

        return $this->filters;
    }

    protected function insertAdditionalFilters($listStandartFilters, $listAdditionalFilters)
    {
        if(count($listAdditionalFilters) == 0) {
            return $listStandartFilters;
        }
        $listNewFilters = [];
        foreach($listStandartFilters as $filter) {
            if(!$filter->hasAttributeModel()) {
                $listNewFilters[] = $filter;
                continue;
            }
            $position = $filter->getAttributeModel()->getPosition();
            foreach($listAdditionalFilters as $key=>$additionalFilter) {
                $additionalFilterPosition = $additionalFilter->getPosition();
                if($additionalFilterPosition <= $position) {
                    $listNewFilters[] = $additionalFilter;
                    unset($listAdditionalFilters[$key]);
                }
            }
            $listNewFilters[] = $filter;
        }
        $listNewFilters = array_merge($listNewFilters, $listAdditionalFilters);
        return $listNewFilters;
    }

    protected function isEnabledShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
