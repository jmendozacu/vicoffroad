<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
namespace Amasty\Shopby\Model\Layer\Filter;

use Amasty\Shopby\Model\Layer\Filter\Traits\FromToDecimal;
use Amasty\Shopby\Model\Source\DisplayMode;


class Price extends \Magento\CatalogSearch\Model\Layer\Filter\Price
    implements \Amasty\Shopby\Api\Data\FromToFilterInterface
{
    use FromToDecimal;

    protected $settingHelper;

    protected $currencySymbol;

    protected $dataProvider;

    protected $aggregationAdapter;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter,
        array $data = []
    ) {
        $this->settingHelper = $settingHelper;
        $this->currencySymbol = $priceCurrency->getCurrencySymbol();
        $this->dataProvider = $dataProviderFactory->create(['layer' => $layer]);
        $this->aggregationAdapter = $aggregationAdapter;
        parent::__construct(
            $filterItemFactory, $storeManager, $layer, $itemDataBuilder,
            $resource, $customerSession, $priceAlgorithm, $priceCurrency,
            $algorithmFactory, $dataProviderFactory, $data
        );
    }

    /**
     * @return array
     */
    public function getFromToConfig()
    {
        $config = [];

        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);

        if ((string)$filterSetting->getDisplayMode() === (string)DisplayMode::MODE_SLIDER ||
            (string)$filterSetting->getDisplayMode() === (string)DisplayMode::MODE_FROM_TO_ONLY ||
            $filterSetting->getAddFromToWidget() === '1') {

            $productCollection = $this->getLayer()->getProductCollection();
            $attribute = $this->getAttributeModel();

            if ($this->hasCurrentValue()) {
                $requestBuilder = clone $productCollection->_memRequestBuilder;
                $requestBuilder->removePlaceholder($attribute->getAttributeCode() . '.from');
                $requestBuilder->removePlaceholder($attribute->getAttributeCode() . '.to');
                $queryRequest = $requestBuilder->create();
                $facets = $this->aggregationAdapter->getBucketByRequest($queryRequest, $attribute->getAttributeCode());
            } else {
                $facets = $productCollection->getFacetedData($attribute->getAttributeCode());
            }

            $min = floatval($facets['data']['min']) * $this->getCurrencyRate();
            $max = floatval($facets['data']['max']) * $this->getCurrencyRate();
            if($min == $max) {
                return [];
            }
            $from = floatval(!empty($this->getCurrentFrom()) ? $this->getCurrentFrom() : $min);
            $to = floatval(!empty($this->getCurrentTo()) ? $this->getCurrentTo() : $max);

            $config =
                [
                    'from' => $from < $min ? $min : $from,
                    'to' => $to > $max ? $max : $to ,
                    'min' => $min,
                    'max' => $max,
                    'requestVar'    => $this->getRequestVar(),
                    'step'          => round($filterSetting->getSliderStep(), 4),
                    'template'      => !$filterSetting->getUnitsLabelUseCurrencySymbol() ? '{amount} '.$filterSetting->getUnitsLabel() : $this->currencySymbol . '{amount}',
                ];
        }

        return $config;
    }

    public function getItemsCount()
    {
        $itemsCount = parent::getItemsCount();
        if ($itemsCount == 0) {
            /**
             * show up filter event don't have any option
             */
            $fromToConfig = $this->getFromToConfig();
            if ($fromToConfig && $fromToConfig['min'] != $fromToConfig['max']) {
                return 1;
            }

        }

        return $itemsCount;
    }

    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();
        if ($this->hasCurrentValue()) {
            $requestBuilder = clone $productCollection->_memRequestBuilder;
            $requestBuilder->removePlaceholder($attribute->getAttributeCode() . '.from');
            $requestBuilder->removePlaceholder($attribute->getAttributeCode() . '.to');
            $queryRequest = $requestBuilder->create();
            $facets = $this->aggregationAdapter->getBucketByRequest($queryRequest, $attribute->getAttributeCode());
        } else {
            $facets = $productCollection->getFacetedData($attribute->getAttributeCode());
        }

        $data = [];
        if (count($facets) > 1) { // two range minimum
            foreach ($facets as $key => $aggregation) {
                $count = $aggregation['count'];
                if (strpos($key, '_') === false) {
                    continue;
                }
                $data[] = $this->prepareData($key, $count, $data);
            }
        }

        if (count($this->getFromToConfig()) && count($data) == 1) {
            $data = [];
        }

        return $data;
    }

    protected function prepareData($key, $count)
    {
        list($from, $to) = explode('_', $key);
        if ($from == '*') {
            $from = $this->getFrom($to);
        }
        if ($to == '*') {
            $to = $this->getTo($to);
        }
        $label = $this->_renderRangeLabel(
            empty($from) ? 0 : $from * $this->getCurrencyRate(),
            empty($to) ? $to : $to * $this->getCurrencyRate()
        );
        $value = $from . '-' . $to . $this->dataProvider->getAdditionalRequestData();

        $data = [
            'label' => $label,
            'value' => $value,
            'count' => $count,
            'from' => $from,
            'to' => $to,
        ];

        return $data;
    }



    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $apply = parent::apply($request);
        $filter = $request->getParam($this->getRequestVar());
        if(!empty($filter) && !is_array($filter)) {
            $filterParams = explode(',', $filter);
            $validateFilter = $this->dataProvider->validateFilter($filterParams[0]);
            if (!$validateFilter) {
                return $this;
            }
            $this->setFromTo($validateFilter[0], $validateFilter[1] - 0.01);
            $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
            if($filterSetting->getDisplayMode() == DisplayMode::MODE_SLIDER) {
                $this->getLayer()->getProductCollection()->addFieldToFilter(
                    'price',
                    ['from' => $this->getCurrentFrom(), 'to' =>  $this->getCurrentTo()]
                );
            }

        }

        return $apply;
    }

//    protected function _renderRangeLabel($fromPrice, $toPrice)
//    {
//        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
//        if($filterSetting->getUnitsLabelUseCurrencySymbol()) {
//            if($filterSetting->getDisplayMode() == DisplayMode::MODE_SLIDER && $fromPrice != $toPrice && $toPrice !== '') {
//                $toPrice += .01;
//            }
//
//            return parent::_renderRangeLabel($fromPrice, $toPrice);
//        }
//        $formattedFromPrice = round($fromPrice, 4).' '.$filterSetting->getUnitsLabel();
//        if ($toPrice === '') {
//            return __('%1 and above', $formattedFromPrice);
//        } else {
//            if ($fromPrice != $toPrice && $filterSetting->getDisplayMode() != DisplayMode::MODE_SLIDER) {
//                $toPrice -= .01;
//            }
//
//            return __('%1 - %2', $formattedFromPrice, round($toPrice, 4).' '.$filterSetting->getUnitsLabel());
//        }
//    }
}
