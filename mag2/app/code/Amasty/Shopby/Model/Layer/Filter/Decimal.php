<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
namespace Amasty\Shopby\Model\Layer\Filter;

use Amasty\Shopby\Model\Layer\Filter\Traits\FromToDecimal;
use Amasty\Shopby\Model\Source\DisplayMode;

class Decimal extends \Magento\CatalogSearch\Model\Layer\Filter\Decimal
    implements \Amasty\Shopby\Api\Data\FromToFilterInterface
{
    use FromToDecimal;

    protected $settingHelper;

    protected $currencySymbol;

    /**
     * @var \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter
     */
    protected $aggregationAdapter;

    protected $dataProvider;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\DecimalFactory $filterDecimalFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        array $data = []
    ) {
        $this->settingHelper = $settingHelper;
        $this->currencySymbol = $priceCurrency->getCurrencySymbol();
        $this->aggregationAdapter = $aggregationAdapter;
        $this->dataProvider = $dataProviderFactory->create(['layer' => $layer]);
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $filterDecimalFactory, $priceCurrency, $data);
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
            $this->setFromTo($validateFilter[0], $validateFilter[1]);
        }

        return $apply;
    }

    public function getItemsCount()
    {
        $itemsCount = parent::getItemsCount();
        /**
         * show up filter event don't have any option
         */
        if (count($this->getFromToConfig()) && $itemsCount == 0) {
            return 1;
        }

        return $itemsCount;
    }

    /**
     * @return array
     */
    public function getFromToConfig()
    {
        $config = [];
        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);

        if ((string)$filterSetting->getDisplayMode() === (string)DisplayMode::MODE_SLIDER ||
            $filterSetting->getAddFromToWidget() === '1') {

            $productCollectionOrigin = $this->getLayer()->getProductCollection();
            $attribute = $this->getAttributeModel();

            if ($this->hasCurrentValue()) {
                $requestBuilder = clone $productCollectionOrigin->_memRequestBuilder;
                $requestBuilder->removePlaceholder($attribute->getAttributeCode() . '.from');
                $requestBuilder->removePlaceholder($attribute->getAttributeCode() . '.to');
                $queryRequest = $requestBuilder->create();
                $facets = $this->aggregationAdapter->getBucketByRequest($queryRequest, $attribute->getAttributeCode());
            } else {
                $facets = $productCollectionOrigin->getFacetedData($attribute->getAttributeCode());
            }

            $min = floatval($facets['data']['min']);
            $max = floatval($facets['data']['max']);
            if($min == $max) {
                return [];
            }
            $from = floatval(!empty($this->getCurrentFrom()) ? $this->getCurrentFrom() : $min);
            $to = floatval(!empty($this->getCurrentTo()) ? $this->getCurrentTo() - 0.01 : $max);

            $config = [
                    'from' => $from < $min ? $min : $from,
                    'to' => $to > $max ? $max : $to ,
                    'min' => $min,
                    'max' => $max,
                    'requestVar' => $this->getRequestVar(),
                    'step' => round($filterSetting->getSliderStep(), 4),
                    'template' => !$filterSetting->getUnitsLabelUseCurrencySymbol() ? '{amount} ' . $filterSetting->getUnitsLabel() : $this->currencySymbol . '{amount}'
                ];

        }

        return $config;
    }

    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();

        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();
        $productSize = $productCollection->getSize();
        $facets = $productCollection->getFacetedData($attribute->getAttributeCode());

        $data = [];
        foreach ($facets as $key => $aggregation) {
            if ($key === 'data'){
                continue;
            }
            $count = $aggregation['count'];
            if (!$this->isOptionReducesResults($count, $productSize)) {
                continue;
            }
            list($from, $to) = explode('_', $key);
            if ($from == '*') {
                $from = '';
            }
            if ($to == '*') {
                $to = '';
            }
            $label = $this->renderRangeLabel(
                empty($from) ? 0 : $from,
                empty($to) ? $to : $to
            );
            $value = $from . '-' . $to;

            $data[] = [
                'label' => $label,
                'value' => $value,
                'count' => $count,
                'from' => $from,
                'to' => $to
            ];
        }

        return $data;
    }

    protected function renderRangeLabel($fromPrice, $toPrice)
    {
        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
        if($filterSetting->getUnitsLabelUseCurrencySymbol()) {
            return parent::renderRangeLabel($fromPrice, $toPrice);
        }
        $formattedFromPrice = round($fromPrice, 4).' '.$filterSetting->getUnitsLabel();
        if ($toPrice === '') {
            return __('%1 and above', $formattedFromPrice);
        } else {
            if ($fromPrice != $toPrice) {
                $toPrice -= .01;
            }
            return __('%1 - %2', $formattedFromPrice, round($toPrice, 4).' '.$filterSetting->getUnitsLabel());
        }
    }
}
