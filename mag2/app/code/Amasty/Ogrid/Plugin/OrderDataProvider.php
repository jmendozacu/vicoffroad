<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */

namespace Amasty\Ogrid\Plugin;


class OrderDataProvider
{
    const SALES_ORDER_GRID_DATA_SOURCE = 'sales_order_grid_data_source';

    protected $_bookmarkManagement;

    protected $_export;

    protected $_columnsForceLoad = [
        'amasty_ogrid_base_subtotal',
        'amasty_ogrid_subtotal'
    ];

    protected $_orderItemCollectionFactory;

    protected $_columns = [];

    protected $_helper;

    protected $_orderConfig = [];

    protected $_filterData = [];

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Amasty\Ogrid\Helper\Data $helper
    ){
        $this->_bookmarkManagement = $bookmarkManagement;
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->_helper = $helper;
        $this->_filterData = $context->getFiltersParams();
    }

    protected function isOrderGrid(\Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $dataProvider)
    {
        return $dataProvider->getName() == self::SALES_ORDER_GRID_DATA_SOURCE;
    }

    protected function _getActiveBookmark(){
        $bookmarks = $this->_bookmarkManagement->loadByNamespace('sales_order_grid');
        $activeBookmark = [];
        $config = [];
        /** @var \Magento\Ui\Api\Data\BookmarkInterface $bookmark */
        foreach ($bookmarks->getItems() as $bookmark) {
            if ($bookmark->isCurrent()) {
                $config['activeIndex'] = $bookmark->getIdentifier();
                $activeBookmark = $config;
            }
            $config = array_merge_recursive($config, $bookmark->getConfig());
        }
        return $activeBookmark;
    }

    protected function _isColumnVisible($bookmark, $column)
    {
        $visible = false;
        if ($this->_isExport()){
            $visible = !in_array($column, ['amasty_ogrid_items_ordered']);
        } else {
            $visible = isset($bookmark['current']['columns']) &&
                isset($bookmark['current']['columns'][$column]) &&
                isset($bookmark['current']['columns'][$column]['visible']) &&
                $bookmark['current']['columns'][$column]['visible'];
        }

        return $visible;

    }

    public function beforeAddFilter(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $dataProvider,
        \Magento\Framework\Api\Filter $filter
    ){
        if ($this->isOrderGrid($dataProvider)){
            if (array_key_exists($filter->getField(), $this->_helper->getOrderFields())) {

                $activeBookmark = $this->_getActiveBookmark();

                if (in_array($filter->getField(), $this->_columnsForceLoad) ||
                    $this->_isColumnVisible($activeBookmark, $filter->getField())){

                    $this->_getColumn($filter->getField())->changeFilter($filter);
                }
            } else {
                $filter->setField('main_table.'.$filter->getField());
            }
        }
    }

    protected function _isExport()
    {
        if ($this->_export === null){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $requestInterface = $objectManager->get('Magento\Framework\App\RequestInterface');

            $this->_export = $requestInterface->getControllerName() === 'export';
        }
        return $this->_export;
    }

    public function afterGetSearchResult(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $dataProvider,
        $collection
    ){
        if ($this->isOrderGrid($dataProvider)) {
            $activeBookmark = $this->_getActiveBookmark();

            foreach ($this->_helper->getOrderFields()as $key => $value) {
                if (in_array($key, $this->_columnsForceLoad) ||
                    $this->_isColumnVisible($activeBookmark, $key)
                ) {
                    $this->_getColumn($key)->addField($collection);
                }
            }
        }

        if (count($this->_helper->getHideStatuses()) > 0){
            $collection->addFieldToFilter('main_table.status', ['nin' => $this->_helper->getHideStatuses()]);
        }

        $this->_applyOrderItemFilters($collection);

        return $collection;
    }

    protected function _applyOrderItemFilters($collection)
    {
        $applyFilter = false;
        $orderItemCollection = $this->_getOrderItemCollection(['items' => []]);

        $this->_prepareOrderItemCollection($orderItemCollection);

        foreach ($this->_helper->getOrderItemFields() as $key => $value) {
            if (array_key_exists($key, $this->_filterData)){
                $applyFilter = true;
                $this->_getColumn($key)->addFieldToFilter($orderItemCollection, $this->_filterData[$key]);
            }
        }

        foreach ($this->_helper->getAttributesFields() as $key => $attribute) {
            if (array_key_exists($attribute->getAttributeDbAlias(), $this->_filterData)){
                $applyFilter = true;
                $attribute->addFieldToFilter($orderItemCollection, $this->_filterData[$attribute->getAttributeDbAlias()]);
            }
        }

        if ($applyFilter){
            $idsSelect = "select DISTINCT order_id from (" . $orderItemCollection->getSelect()->__toString() . ") as tmp";

            $from = $collection->getSelect()->getPart(\Zend_Db_Select::FROM);

            $from['amasty_ogrid_order_item_filter'] = array(
                'joinType' => 'inner join',
                'schema' => null,
                'tableName' => new \Zend_Db_Expr('(' . $idsSelect . ')'),
                'joinCondition' => 'main_table.entity_id = order_id'
            );

            $collection->getSelect()->setPart(\Zend_Db_Select::FROM, $from);
        }
    }

    protected function _getColumn($key)
    {
        if (!array_key_exists($key, $this->_columns)){
            $this->_columns[$key] = \Magento\Framework\App\ObjectManager::getInstance()->create('Amasty\\Ogrid\\Model\\Column\\' . $this->_helper->getOrderField($key));
        }
        return $this->_columns[$key];
    }

    protected function _getOrderItemCollection($data)
    {
        $orderItemCollection = null;
        if (array_key_exists('items', $data)) {
            $orderIds = [];
            foreach($data['items'] as $item){
                $orderIds[] = $item['entity_id'];
                $this->_orderConfig[$item['entity_id']] = [
                    'order_currency_code' => $item['order_currency_code'],
                    'base_currency_code' => $item['base_currency_code']
                ];
            }

            $orderItemCollection = $this->_orderItemCollectionFactory->create();

            if (count($orderIds) > 0 ){
                $orderItemCollection
                    ->addFieldToFilter('order_id', ['in' => $orderIds]);
            }

            $orderItemCollection->getSelect()->joinLeft(
                [\Amasty\Ogrid\Model\Attribute::TABLE_ALIAS => $orderItemCollection->getTable('amasty_ogrid_attribute_index')],
                \Amasty\Ogrid\Model\Attribute::TABLE_ALIAS . '.order_item_id = main_table.item_id',
                []
            );

            $orderItemCollection->getSelect()->setPart(\Zend_Db_Select::COLUMNS, []);
        }

        return $orderItemCollection;
    }

    public function _prepareOrderItemCollection($orderItemCollection)
    {
        $activeBookmark = $this->_getActiveBookmark();

        foreach ($this->_helper->getOrderItemFields() as $key => $value) {
            if ($this->_isColumnVisible($activeBookmark, $key)) {
                $this->_getColumn($key)->addFieldToSelect($orderItemCollection);
            }
        }

        foreach($this->_helper->getAttributeCollection() as $attribute){
            if ($this->_isColumnVisible($activeBookmark, $attribute->getAttributeDbAlias())) {
                $attribute->addFieldToSelect($orderItemCollection);
            }
        }

        $orderItemCollection->getSelect()->columns(['order_id', 'item_id', 'parent_item_id']);
    }

    public function _modifyOrderItemData(&$orderItemData)
    {
        $orderItemField = $this->_helper->getOrderItemFields();
        $activeBookmark = $this->_getActiveBookmark();
        $attributesCollection = $this->_helper->getAttributeCollection();


        $reorderedData = [];
        $childData = [];

        foreach($orderItemData as $idx => &$orderItem){
            $orderId = $orderItem['order_id'];
            $parentItemId = $orderItem['parent_item_id'];
            $itemId = $orderItem['item_id'];

            foreach($orderItemField as $key => $value) {
                if ($this->_isColumnVisible($activeBookmark, $key)) {
                    $this->_getColumn($key)->modifyItem($orderItem, $this->_orderConfig);
                }
            }

            foreach($attributesCollection as $attribute){
                if ($this->_isColumnVisible($activeBookmark, $attribute->getAttributeDbAlias())) {
                    $attribute->modifyItem($orderItem, $this->_orderConfig);
                }
            }

            if ($parentItemId === null){
                $reorderedData[$orderId][$itemId] = $orderItem;
            } else {
                $childData[$parentItemId] = $orderItem;
            }
        }

        $this->_moveDataFromChildToParent($reorderedData, $childData);

        $orderItemData = $reorderedData;
    }

    protected function _moveDataFromChildToParent(&$reorderedData, $childData)
    {
        $attributesCollection = $this->_helper->getAttributeCollection();
        $activeBookmark = $this->_getActiveBookmark();

        foreach($reorderedData as &$orderData){
            foreach($orderData as $orderItemId => &$orderItem){
                if (array_key_exists($orderItemId, $childData)){
                    $childItem = $childData[$orderItemId];

                    foreach($attributesCollection as $attribute){
                        if ($this->_isColumnVisible($activeBookmark, $attribute->getAttributeDbAlias())){
                            $value = [];
                            $childValue = $childItem[$attribute->getAttributeDbAlias()];
                            $parentValue = $orderItem[$attribute->getAttributeDbAlias()];

                            if (!is_array($parentValue) && $parentValue !== null){
                                $value = [$parentValue];
                            } else if (is_array($parentValue)) {
                                $value = $parentValue;
                            }

                            if (!is_array($childValue) && $childValue !== null){
                                $value = array_merge($value, [$childValue]);
                            } else if (is_array($childValue)) {
                                $value = array_merge($value, $childValue);
                            }

                            $orderItem[$attribute->getAttributeDbAlias()] = $value;
                        }
                    }
                }
            }
        }
    }

    public function afterGetData(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $dataProvider,
        $data
    ){
        if ($this->isOrderGrid($dataProvider)) {

            $orderItemCollection = $this->_getOrderItemCollection($data);

            $this->_prepareOrderItemCollection($orderItemCollection);

            $orderItemData = $orderItemCollection->getData();

            $this->_modifyOrderItemData($orderItemData);

            $items = &$data['items'];
            foreach($items as $idx => &$element) {
                $element['amasty_ogrid_items_ordered'] = [];
                $itemsOrdered = &$element['amasty_ogrid_items_ordered'];

                if (array_key_exists($element['entity_id'], $orderItemData)){
                    $itemsOrdered = $orderItemData[$element['entity_id']];
                }
            }
        }

        return $data;
    }
}
