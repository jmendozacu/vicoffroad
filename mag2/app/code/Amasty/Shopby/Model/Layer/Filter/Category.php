<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
namespace Amasty\Shopby\Model\Layer\Filter;



/**
 * Layer category filter
 */
class Category extends \Magento\CatalogSearch\Model\Layer\Filter\Category
{
    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $settingHelper;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory, $storeManager, $layer, $itemDataBuilder,
            $escaper, $categoryDataProviderFactory, $data
        );
        $this->settingHelper = $settingHelper;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $itemsData = parent::_getItemsData();
        $setting = $this->settingHelper->getSettingByLayerFilter($this);
        if ($setting->getHideOneOption()) {
            if (count($itemsData) == 1) {
                $itemsData = [];
            }
        }

        if($setting->getSortOptionsBy() == \Amasty\Shopby\Model\Source\SortOptionsBy::NAME) {
            usort($itemsData, [$this, 'sortOption']);
        }

        return $itemsData;
    }

    public function sortOption($a, $b)
    {
        return strcmp($a['label'], $b['label']);
    }


}
