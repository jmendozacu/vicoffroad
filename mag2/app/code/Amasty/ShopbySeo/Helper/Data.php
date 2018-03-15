<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbySeo\Helper;


use Amasty\Shopby\Api\Data\FilterSettingInterface;
use Magento\Catalog\Model\Layer;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManager;
use Amasty\Shopby\Model\ResourceModel\FilterSetting\CollectionFactory;
use Amasty\Shopby\Model\ResourceModel\OptionSetting\CollectionFactory as OptionSettingCollectionFactory;

class Data extends AbstractHelper
{
    /** @var CollectionFactory */
    protected $settingCollectionFactory;
    /** @var Option\CollectionFactory */
    protected $optionCollectionFactory;
    /** @var  OptionSettingCollectionFactory */
    protected $optionSettingCollectionFactory;
    /** @var  StoreManager */
    protected $storeManager;

    /** @var  \Magento\Catalog\Model\Product\Url */
    protected $productUrl;

    protected $seoSignificantUrlParameters;
    protected $optionsSeoData;

    public function __construct(
        Context $context,
        CollectionFactory $settingCollectionFactory,
        Option\CollectionFactory $optionCollectionFactory,
        \Magento\Catalog\Model\Product\Url $productUrl,
        OptionSettingCollectionFactory $optionSettingCollectionFactory,
        StoreManager $storeManager
    )
    {
        parent::__construct($context);
        $this->settingCollectionFactory = $settingCollectionFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->optionSettingCollectionFactory = $optionSettingCollectionFactory;
        $this->storeManager = $storeManager;
        $this->productUrl = $productUrl;
    }

    public function getSeoSignificantUrlParameters()
    {
        if (is_null($this->seoSignificantUrlParameters)) {
            $this->seoSignificantUrlParameters = $this->getSeoSignificantAttributeCodes();
        }
        return $this->seoSignificantUrlParameters;
    }

    public function getOptionsSeoData()
    {
        if (is_null($this->optionsSeoData)) {
            $this->optionsSeoData = [];
            $aliasHash = [];

            $hardcodedAliases = $this->loadHardcodedAliases();
            foreach ($hardcodedAliases as $row) {
                $alias = $this->buildUniqueAlias($row['url_alias'], $aliasHash);
                if (strpos($row['filter_code'], 'attr_') === 0) {
                    $attributeCode = substr($row['filter_code'], strlen('attr_'));
                } else {
                    $attributeCode = '';
                }
                $this->optionsSeoData[$row['value']] = [
                    'alias' => $alias,
                    'attribute_code' => $attributeCode,
                ];
                $aliasHash[$alias] = $row['value'];
            }

            $dynamicAliases = $this->loadDynamicAliases();
            foreach ($dynamicAliases as $row) {
                if (in_array($row['option_id'], $aliasHash))
                {
                    continue;
                }
                $alias = $this->buildUniqueAlias($row['value'], $aliasHash);
                $optionId = $row['option_id'];
                $this->optionsSeoData[$optionId] = [
                    'alias' => $alias,
                    'attribute_code' => $row['attribute_code'],
                ];
                $aliasHash[$alias] = $optionId;
            }
        }
        return $this->optionsSeoData;
    }

    protected function loadHardcodedAliases()
    {
        $select = $this->optionSettingCollectionFactory->create()->getSelect();
        $storeId = $this->storeManager->getStore()->getId();
        $listStores = '0';
        if($storeId > 0) {
            $listStores .= ',' . $storeId;
        }
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->columns('url_alias');
        $select->columns('filter_code');
        $select->columns('value');
        $select->where('`url_alias` <> ""');
        $select->where('`store_id` IN (' . $listStores . ')');
        $select->order('store_id ASC');
        $data = $select->getConnection()->fetchAll($select);
        return $data;
    }

    protected function loadDynamicAliases()
    {
        $seoAttributeCodes = $this->getSeoSignificantAttributeCodes();

        $collection = $this->optionCollectionFactory->create();
        $collection->join(['a' => 'eav_attribute'], 'a.attribute_id = main_table.attribute_id', ['attribute_code']);
        $collection->addFieldToFilter('attribute_code', ['in' => $seoAttributeCodes]);
        $collection->setStoreFilter();
        $select = $collection->getSelect();

        $statement = $select->query();
        $rows = $statement->fetchAll();
        return $rows;
    }

    protected function getSeoSignificantAttributeCodes()
    {
        $collection = $this->settingCollectionFactory->create();
        $collection->addFieldToFilter(FilterSettingInterface::IS_SEO_SIGNIFICANT, 1);
        $filterCodes = $collection->getColumnValues(FilterSettingInterface::FILTER_CODE);
        array_walk($filterCodes, function (&$code) {
            if (substr($code, 0, 5) == \Amasty\Shopby\Helper\FilterSetting::ATTR_PREFIX) {
                $code = substr($code, 5);
            }
        });
        return $filterCodes;
    }

    protected function buildUniqueAlias($value, $hash)
    {
        if (preg_match('@^[\d\.]+$@s', $value)) {
            $format = $value;
        } else {
            $format = $this->productUrl->formatUrlKey($value);
        }
        if ($format == '') {
            // Magento formats '-' as ''
            $format = '-';
        }

        $unique = $format;
        for ($i=1; array_key_exists($unique, $hash); $i++) {
            $unique = $format . '-' . $i;
        }
        return $unique;
    }
}
