<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Helper;


use Amasty\Shopby\Model\Layer\Filter\Price;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;

class UrlBuilder extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $filterSettingHelper;

    /** @var  Registry */
    protected $registry;

    /** @var \Magento\Framework\Url\QueryParamsResolverInterface  */
    protected $queryParamsResolver;

    /** @var  FilterInterface */
    protected $filter;

    public function __construct(
        Context $context,
        Registry $registry,
        \Amasty\Shopby\Helper\FilterSetting $filterSettingHelper,
        \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver
    )
    {
        parent::__construct($context);
        $this->registry = $registry;
        $this->filterSettingHelper = $filterSettingHelper;
        $this->queryParamsResolver = $queryParamsResolver;
    }


    public function buildUrl(FilterInterface $filter, $optionValue)
    {
        $this->filter = $filter;

        $routePath = '*/*/*';

        if ($filter instanceof Price && is_array($optionValue)) {
            $optionValue = implode('-', $optionValue);
        }
        $currentValues = $this->getCurrentValues();
        $resultValue = $this->calculateResultValue($optionValue, $currentValues);

        $query = $this->registry->registry('amasty_shopby_seo_parsed_params');
        if (!is_array($query)) {
            $query = [];
        }
        $query[$filter->getRequestVar()] = $resultValue;
        $query['isAjax'] = null;
        $query['_'] = null;

        return $this->_urlBuilder->getUrl($routePath, ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    protected function getCurrentValues()
    {
        $data = $this->_request->getParam($this->filter->getRequestVar());
        if(!empty($data)){
            $values = explode(',',$data);
            foreach($values as $key=>$val){
                if(empty($val)){
                    unset($values[$key]);
                }
            }
        } else {
            $values = [];
        }

        return $values;
    }

    /**
     * @param $optionValue
     * @param array $currentValues
     * @return string|null
     */
    protected function calculateResultValue($optionValue, array $currentValues)
    {
        $key = array_search($optionValue, $currentValues);

        if ($this->isMultiselectAllowed()) {
            $result = $currentValues;

            if($key !== false) {
                unset($result[$key]);
            }else{
                $result[] = $optionValue;
            }
        } else {
            if($key !== false) {
                $result = [];
            } else {
                $result = [$optionValue];
            }
        }

        $value = $result ? implode(',', $result) : null;
        return $value;
    }

    protected function isMultiselectAllowed()
    {
        $setting = $this->filterSettingHelper->getSettingByLayerFilter($this->filter);
        return $setting->isMultiselect();
    }
}
