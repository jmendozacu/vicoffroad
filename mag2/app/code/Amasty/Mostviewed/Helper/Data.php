<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Mostviewed\Helper;

use Magento\Framework\App\Helper\Context;
use Amasty\Mostviewed\Model\Config\Source\Condition\Price;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $stockHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->coreRegistry = $registry;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->stockHelper = $stockHelper;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }


    public function getBlockConfig($block, $config)
    {
        return $this->getCurrentStoreConfig('ammostviewed/' . $block . '/' . $config);
    }

    /**
     * @param       $productId
     * @param       $block
     * @param array $exclude
     * @param \Magento\Catalog\Model\Config $catalogConfig
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection | \Magento\Framework\Data\Collection
     */
    public function getViewedWith(\Magento\Catalog\Model\Product $product, $block, $exclude = [], \Magento\Catalog\Model\Config $catalogConfig = null, $appendCollection = null)
    {
        $productId = $product->getId();
        $size = intVal($this->getBlockConfig($block, 'size'));
        if (!$size) {
            return $this->objectManager->create('Magento\Framework\Data\Collection');
        }

        if(!is_null($appendCollection) && count($appendCollection) >= $size) {
            return $appendCollection;
        }

        if(!is_null($appendCollection)) {
            $size -= count($appendCollection);
        }
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $product->getResourceCollection();

        if ($this->getBlockConfig($block, 'data_source') == \Amasty\Mostviewed\Model\Config\Source\DataSource::SOURCE_VIEWED) {
            $ids = $this->_getRelatedIdsViewed($collection, $productId, $block);
        } else {
            $ids = $this->_getRelatedIdsBought($collection, $product, $block);
        }

        if (!count($ids)) {
            return $this->objectManager->create('Magento\Framework\Data\Collection');
        }


        $collection->addIdFilter(array_diff($ids, $exclude));

        $this->_addPricesAndAttributes($collection, $catalogConfig);
        $this->_addCommonFilters($collection, $block);
        if ($block != 'cross_sells') {
            $this->addCategopryFilter($collection, $product, $block);
            $this->addBrandFilter($collection, $product, $block);
            $this->addPriceFilter($collection, $product, $block);
        }

        $collection->getSelect()->limit($size);

        $used = [];
        foreach ($collection as $product) {
            $product->setDoNotUseCategoryId(true);
            if(!is_null($appendCollection)) {
                if(is_object($appendCollection)) {
                    $appendCollection->addItem($product);
                } else {
                    $appendCollection[] = $product;
                }
            }
            $used[] = $product->getId();
        }

        if (!empty($used) && !$this->coreRegistry->registry('ammostviewed_used')) {
            $this->coreRegistry->register('ammostviewed_used', $used, true);
        }

        return !is_null($appendCollection) ? $appendCollection : $collection;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return $this
     */
    protected function _addPricesAndAttributes(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection, \Magento\Catalog\Model\Config $catalogConfig = null)
    {
        $collection->addAttributeToSelect(
            'required_options'
        )->setOrder('position', \Magento\Framework\DB\Select::SQL_ASC)->addStoreFilter();

        if ($this->_moduleManager->isEnabled('Magento_Checkout')) {
            $collection
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addUrlRewrite();
            if (!is_null($catalogConfig)) {
                $collection->addAttributeToSelect($catalogConfig->getProductAttributes());
            }
        }
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        return $collection;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param                                                         $block
     *
     * @return $this
     */
    protected function _addCommonFilters(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection, $block)
    {
        // remove out of stock items
        $inStock = $this->getBlockConfig($block, 'in_stock');
        if ($inStock && $this->_moduleManager->isEnabled('Magento_CatalogInventory')) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param                                                         $id
     * @param                                                         $block
     *
     * @return array
     */
    protected function _getRelatedIdsViewed(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection, $id, $block)
    {
        $tbl = $collection->getTable('report_viewed_product_index');
        $db = $collection->getConnection();
        $storeId = $this->storeManager->getStore()->getId();

        $period = $this->getBlockConfig($block, 'period');
        if (!$period) {
            $period = 1000;
        }

        $queryLimit = $this->getCurrentStoreConfig('ammostviewed/general/limit');
        if (!$queryLimit) {
            $queryLimit = 1000;
        }

        //get visitors who viewed this product
        $visitors = $db->select()->from(array('t2' => $tbl), array('visitor_id'))
            ->where('product_id = ?', $id)
            ->where('visitor_id IS NOT NULL')
            ->where('store_id = ?', $storeId)
            ->where('TO_DAYS(NOW()) - TO_DAYS(added_at) <= ?', $period)
            ->limit($queryLimit);

        //get customers who viewed this product
        $customers = $db->select()->from(array('t2' => $tbl), array('customer_id'))
            ->where('product_id = ?', $id)
            ->where('customer_id IS NOT NULL')
            ->where('store_id = ?', $storeId)
            ->where('TO_DAYS(NOW()) - TO_DAYS(added_at) <= ?', $period)
            ->limit($queryLimit);

        $visitors = array_unique($db->fetchCol($visitors));
        $customers = array_unique($db->fetchCol($customers));
        $customers = array_diff($customers, $visitors);

        // get related products
        $fields = array(
            'id' => 't.product_id',
            'cnt' => new \Zend_Db_Expr('COUNT(*)'),
        );
        $productsByVisitor = $db->select()->from(array('t' => $tbl), $fields)
            ->where('t.visitor_id IN (?)', $visitors)
            ->where('t.product_id != ?', $id)
            ->where('store_id = ?', $storeId)
            ->group('t.product_id')
            ->order('cnt DESC')
            ->limit($queryLimit);
        $productsByVisitor = $db->fetchAll($productsByVisitor);

        $productsByCustomer = $db->select()->from(array('t' => $tbl), $fields)
            ->where('t.customer_id IN (?)', $customers)
            ->where('t.product_id != ?', $id)
            ->where('store_id = ?', $storeId)
            ->group('t.product_id')
            ->order('cnt DESC')
            ->limit($queryLimit);
        $productsByCustomer = $db->fetchAll($productsByCustomer);

        $data = array_merge($productsByVisitor, $productsByCustomer);

        $views = array();
        $products = array();
        foreach ($data as $key => $row) {
            $views[$key] = $row['cnt'];
            $products[$key] = $row['id'];
        }

        array_multisort($views, SORT_DESC, $products);

        return array_unique($products);
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \Magento\Catalog\Model\Product $product
     * @param                                                         $block
     *
     * @return array
     */
    protected function _getRelatedIdsBought(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection, \Magento\Catalog\Model\Product $product, $block)
    {
        $tbl = $collection->getTable('sales_order_item');
        $db = $collection->getConnection();
        $storeId = $this->storeManager->getStore()->getId();

        $period = $this->getBlockConfig($block, 'period');
        if (!$period) {
            $period = 1000;
        }

        $queryLimit = $this->getCurrentStoreConfig('ammostviewed/general/limit');
        if (!$queryLimit) {
            $queryLimit = 1000;
        }
        $productIds = [];

        $productType = $product->getTypeId();
        $typeInstance = $product->getTypeInstance();

        switch ($productType) {
            case 'grouped':
                $productIds = $typeInstance->getAssociatedProductIds($product);
                break;
            case 'configurable':
                $productIds = $typeInstance->getUsedProductIds($product);
                break;
            case 'bundle':
                $optionsIds = $typeInstance->getOptionsIds($product);
                $selections = $typeInstance->getSelectionsCollection($optionsIds, $product);
                foreach ($selections as $selection) {
                    $productIds[] = $selection->getProductId();
                }
                break;
            default:
                $productIds[] = $product->getId();
        }

        //get customer who bought this product
        $customers = $db->select()->from(array('order_item' => $tbl), [])
            ->join(
                ['order' => $db->getTableName('sales_order')],
                'order_item.order_id = order.entity_id',
                ['customer_id' => 'order.customer_id']
            )
            ->where('order_item.product_id IN(?)', $productIds)
            ->where('order.customer_id IS NOT NULL')
            ->where('order_item.store_id = ?', $storeId)
            ->where('TO_DAYS(NOW()) - TO_DAYS(order.created_at) <= ?', $period)
            ->limit($queryLimit);
        $customers = array_unique($db->fetchCol($customers));

        $guests = $db->select()->from(array('order_item' => $tbl), [])
            ->join(
                ['order' => $db->getTableName('sales_order')],
                'order_item.order_id = order.entity_id',
                ['customer_id' => 'order.customer_email']
            )
            ->where('order_item.product_id IN(?)', $productIds)
            ->where('order.customer_is_guest = 1')
            ->where('order_item.store_id = ?', $storeId)
            ->where('TO_DAYS(NOW()) - TO_DAYS(order.created_at) <= ?', $period)
            ->limit($queryLimit);
        $guests = array_unique($db->fetchCol($guests));


        $productIdField = new \Zend_Db_Expr('
            IF(configurable.parent_id IS NOT NULL, configurable.parent_id, IF(bundle.parent_product_id IS NOT NULL, bundle.parent_product_id, order_item.product_id))
        ');
        $productsByCustomers = $db->select()->from(array('order_item' => $tbl), ['id' => $productIdField, 'cnt' => new \Zend_Db_Expr('COUNT(*)')])
            ->join(
                ['order' => $db->getTableName('sales_order')],
                'order_item.order_id = order.entity_id',
                []
            )
            ->joinLeft(
                ['configurable' => $db->getTableName('catalog_product_super_link')],
                'order_item.product_id = configurable.product_id',
                []
            )
            ->joinLeft(
                ['bundle' => $db->getTableName('catalog_product_bundle_selection')],
                'order_item.product_id = bundle.product_id',
                []
            )
            ->where('order_item.product_id NOT IN(?)', $productIds)
            ->where('order.customer_id IN(?)', $customers)
            ->where('order_item.store_id = ?', $storeId)
            ->group('order_item.product_id')
            ->order('cnt DESC')
            ->limit($queryLimit);
        $productsByCustomers = $db->fetchAll($productsByCustomers);

        $productsByGuests = $db->select()->from(array('order_item' => $tbl), ['id' => $productIdField, 'cnt' => new \Zend_Db_Expr('COUNT(*)')])
            ->join(
                ['order' => $db->getTableName('sales_order')],
                'order_item.order_id = order.entity_id',
                []
            )
            ->joinLeft(
                ['configurable' => $db->getTableName('catalog_product_super_link')],
                'order_item.product_id = configurable.product_id',
                []
            )
            ->joinLeft(
                ['bundle' => $db->getTableName('catalog_product_bundle_selection')],
                'order_item.product_id = bundle.product_id',
                []
            )
            ->where('order_item.product_id NOT IN(?)', $productIds)
            ->where('order.customer_email IN(?)', $guests)
            ->where('order_item.store_id = ?', $storeId)
            ->group('order_item.product_id')
            ->order('cnt DESC')
            ->limit($queryLimit);
        $productsByGuests = $db->fetchAll($productsByGuests);

        $data = array_merge($productsByGuests, $productsByCustomers);

        $views = array();
        $products = array();
        foreach ($data as $key => $row) {
            $views[$key] = $row['cnt'];
            $products[$key] = $row['id'];
        }

        array_multisort($views, SORT_DESC, $products);

        return array_unique($products);

    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \Magento\Catalog\Model\Product $product
     * @param                                                         $block
     *
     * @return $this
     */
    protected function addCategopryFilter(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection, \Magento\Catalog\Model\Product $product, $block)
    {
        $categorySetting = $this->getBlockConfig($block, 'category_condition');
        if (!$categorySetting) {
            if ($this->coreRegistry->registry('ammostviewed_used')) {
                $collection->addIdFilter($this->coreRegistry->registry('ammostviewed_used'), true);
            }
            return $this;
        }

        $category = $this->coreRegistry->registry('current_category');
        if (!$category) {
            $category = $this->getCategoryByProduct($product);
        }

        if ($category) {
            $exclude = [];
            if (\Amasty\Mostviewed\Model\Config\Source\Condition\Category::SAME_AS == $categorySetting) {
                $collection->addCategoryFilter($category);
            }

            if ($this->coreRegistry->registry('ammostviewed_used')) {
                $exclude = array_merge($exclude, $this->coreRegistry->registry('ammostviewed_used'));
            }
            if (!empty($exclude)) {
                $collection->addIdFilter($exclude, true);
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \Magento\Catalog\Model\Product $product
     * @param                                                         $block
     *
     * @return $this
     */
    protected function addBrandFilter(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection, \Magento\Catalog\Model\Product $product, $block)
    {
        $enabled = $this->getBlockConfig($block, 'brand_condition');
        if (!$enabled || is_null($product->getId())) {
            return $this;
        }
        $brandAttribute = $this->getBlockConfig($block, 'brand_attribute');
        $brandAttributeValue = $product->getData($brandAttribute);
        if (is_null($brandAttributeValue)) {
            $resource = $product->getResource();
            $brandAttributeValue = $resource->getAttributeRawValue($product->getId(), $brandAttribute, $product->getStoreId());
        }

        $collection->addAttributeToFilter($brandAttribute, $brandAttributeValue);


        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \Magento\Catalog\Model\Product $product
     * @param                                                         $block
     *
     * @return $this
     */
    protected function addPriceFilter(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection, \Magento\Catalog\Model\Product $product, $block)
    {
        $priceCondition = $this->getBlockConfig($block, 'price_condition');
        if (!$priceCondition || is_null($product->getId())) {
            return $this;
        }
        $price = $product->getData(\Magento\Catalog\Model\Product::PRICE);

        switch ($priceCondition) {
            case Price::SAME_AS:
                $collection->addFieldToFilter('price', $price);
                break;
            case Price::LESS:
                $collection->addFieldToFilter('price', ['lt' => $price]);
                break;
            case Price::MORE:
                $collection->addFieldToFilter('price', ['gt' => $price]);
                break;
        }

        return $this;
    }

    protected function getCategoryByProduct(\Magento\Catalog\Model\Product $product)
    {
        $ids = [];
        if ($product->getId()) {
            $categories = $product->getCategoryCollection();

            $catPaths = array();
            if (0 < $categories->getSize()) {
                foreach ($categories as $category)
                    $catPaths[] = array_reverse($category->getPathIds());
            }

            if (empty($catPaths)) {
                $catPaths = array(array(\Magento\Catalog\Model\Category::TREE_ROOT_ID));
            }

            $distances = array();

            foreach ($catPaths as $pathIndex => $path) {
                foreach ($path as $categoryIndex => $category) {
                    if (isset($distances[$category])) {
                        $distances[$category]['distance'] = min(
                            $categoryIndex,
                            $distances[$category]
                        );
                    } else {
                        $distances[$category] = array(
                            'distance' => $categoryIndex,
                            'path' => $pathIndex
                        );
                    }
                }
            }

            $ids = array_keys($distances);
        }
        if (!$ids) {
            return null;
        }
        //$categoryId = array_pop($ids);
        $categoryId = array_shift($ids);
        $category = $this->objectManager->create(
            'Magento\Catalog\Model\Category'
        )
            ->setStoreId($this->storeManager->getStore()->getId())
            ->load($categoryId);
        return $category;
    }


    protected function getCurrentStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    protected function getManuallyProductIds(\Magento\Catalog\Model\Product $product, $block)
    {

        switch($block) {
            case 'related_products':
                $ids = $product->getRelatedProductIds();
                break;
            case 'up_sells':
                $ids = $product->getUpSellProductIds();
                break;
            case 'cross_sells':
                break;

        }
    }
}
