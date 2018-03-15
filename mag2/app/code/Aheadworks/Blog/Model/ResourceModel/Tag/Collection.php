<?php
namespace Aheadworks\Blog\Model\ResourceModel\Tag;

use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;

/**
 * Class Collection
 * @package Aheadworks\Blog\Model\ResourceModel\Tag
 */
class Collection extends \Aheadworks\Blog\Model\ResourceModel\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Aheadworks\Blog\Model\Tag', 'Aheadworks\Blog\Model\ResourceModel\Tag');
    }

    /**
     * @param int $categoryId
     * @return $this
     */
    public function addCategoryFilter($categoryId)
    {
        $this->getSelect()
            ->joinLeft(
                ['post_linkage_table' => $this->getTable('aw_blog_post_tag')],
                'main_table.id = post_linkage_table.tag_id',
                []
            )
            ->joinLeft(
                ['category_linkage_table' => $this->getTable('aw_blog_post_cat')],
                'post_linkage_table.post_id = category_linkage_table.post_id',
                []
            )
            ->where('category_linkage_table.cat_id = ?', $categoryId)
            ->group('main_table.id');
        return $this;
    }

    /**
     * Filter collection by post visibility
     *
     * @param int $storeId
     * @return $this
     */
    public function addPostsVisibilityFilter($storeId)
    {
        $now = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time());
        $this->getSelect()
            ->joinLeft(
                ['post_linkage_table_2' => $this->getTable('aw_blog_post_tag')],
                'main_table.id = post_linkage_table_2.tag_id',
                []
            )
            ->joinLeft(
                ['post_table' => $this->getTable('aw_blog_post')],
                'post_linkage_table_2.post_id = post_table.post_id',
                []
            )
            ->joinLeft(
                ['store_linkage_table' => $this->getTable('aw_blog_post_store')],
                'post_table.post_id = store_linkage_table.post_id',
                []
            )
            ->where('store_linkage_table.store_id IN(?)', [$storeId, 0])
            ->where('post_table.status = ?', PostStatus::PUBLICATION)
            ->where('post_table.publish_date <= ?', $now)
            ->group('main_table.id');
        return $this;
    }
}
