<?php
namespace Aheadworks\Blog\Model\ResourceModel\Category;

use Aheadworks\Blog\Model\Category;

/**
 * Class Collection
 * @package Aheadworks\Blog\Model\ResourceModel\Category
 */
class Collection extends \Aheadworks\Blog\Model\ResourceModel\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Aheadworks\Blog\Model\Category', 'Aheadworks\Blog\Model\ResourceModel\Category');
        $this->_map['fields']['cat_id'] = 'main_table.cat_id';
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('cat_id', 'name');
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('cat_id', 'name');
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
        return parent::_afterLoad();
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $stores = [$storeId, 0]; //include all store views
        $this->getSelect()->group('main_table.cat_id');
        return $this->addLinkageInstanceFilter(
            $stores,
            $this->getLinkageTable('store'),
            $this->getResource()->getIdFieldName(),
            'store_id'
        );
    }

    /**
     * @return $this
     */
    public function addEnabledFilter()
    {
        return $this->addFieldToFilter('status', ['eq' => Category::STATUS_ENABLED]);
    }
}
