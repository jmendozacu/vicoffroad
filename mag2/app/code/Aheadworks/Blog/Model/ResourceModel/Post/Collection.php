<?php
namespace Aheadworks\Blog\Model\ResourceModel\Post;

use \Aheadworks\Blog\Model\Source\Post\Status;
use \Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Aheadworks\Blog\Model\ResourceModel\Post
 */
class Collection extends \Aheadworks\Blog\Model\ResourceModel\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Aheadworks\Blog\Model\Post', 'Aheadworks\Blog\Model\ResourceModel\Post');
        $this->_map['fields']['post_id'] = 'main_table.post_id';
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
     * @return $this
     */
    public function addPublishedFilter()
    {
        $now = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time());
        $this->addFieldToFilter('publish_date', ['lteq' => $now])
            ->addFieldToFilter('status', Status::PUBLICATION);
        return $this;
    }

    /**
     * Filter by virtual statuses
     *
     * @param array $allowedStatuses
     * @return $this
     */
    public function addStatusFilter($allowedStatuses)
    {
        if (!is_array($allowedStatuses)) {
            $allowedStatuses = [$allowedStatuses];
        }

        $conn = $this->getConnection();
        $conditions = [];
        $now = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time());

        if (in_array(Status::DRAFT, $allowedStatuses)) {
            $conditions[] = $conn->quoteInto("status = ?", Status::DRAFT);
        }
        if (in_array(Status::PUBLICATION_PUBLISHED, $allowedStatuses)) {
            $conditions[] =
                $conn->quoteInto("status = ?", Status::PUBLICATION) .
                " AND " .
                $conn->quoteInto("publish_date <= ?", $now)
            ;
        }
        if (in_array(Status::PUBLICATION_SCHEDULED, $allowedStatuses)) {
            $conditions[] =
                $conn->quoteInto("status = ?", Status::PUBLICATION) .
                " AND " .
                $conn->quoteInto("publish_date > ?", $now)
            ;
        }

        $this->getSelect()->where(implode(" OR ", $conditions));
        return $this;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $stores = [$storeId, 0];
        $this->getSelect()->group('main_table.post_id');
        return $this->addLinkageInstanceFilter(
            $stores,
            $this->getLinkageTable('store'),
            $this->getResource()->getIdFieldName(),
            'store_id'
        );
    }

    /**
     * @param int $categoryId
     * @return $this
     */
    public function addCategoryFilter($categoryId)
    {
        return $this->addLinkageInstanceFilter(
            $categoryId,
            $this->getLinkageTable('cat'),
            $this->getResource()->getIdFieldName(),
            'cat_id'
        );
    }

    /**
     * @param int $tagId
     * @return $this
     */
    public function addTagFilter($tagId)
    {
        return $this->addLinkageInstanceFilter(
            $tagId,
            $this->getLinkageTable('tag'),
            $this->getResource()->getIdFieldName(),
            'tag_id'
        );
    }
}
