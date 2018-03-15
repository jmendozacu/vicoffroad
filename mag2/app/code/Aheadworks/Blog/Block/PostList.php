<?php
namespace Aheadworks\Blog\Block;

use Aheadworks\Blog\Helper\Config;

/**
 * List of posts block
 * @package Aheadworks\Blog\Block
 */
class PostList extends PostAbstract
{
    /**
     * Init post collection
     *
     * @param \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $collectionFactory
     */
    protected function initPostCollection($collectionFactory)
    {
        parent::initPostCollection($collectionFactory);
        if ($currentTag = $this->getCurrentTagModel()) {
            $this->postCollection->addTagFilter($currentTag->getId());
        }
    }

    /**
     * Preparing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getPostCollection()) {
            /** @var \Aheadworks\Blog\Block\PostList\Pager $pager */
            $pager = $this->getChildBlock('pager');
            if ($pager) {
                $pager->setPath(trim($this->getRequest()->getPathInfo(), '/'));
                $pager->setLimit($this->configHelper->getValue(Config::XML_GENERAL_POSTS_PER_PAGE));
                $pager->setCollection($this->getPostCollection());
                $this->getPostCollection()->load();
            }
        }
        return $this;
    }

    /**
     * Retrieves list item html
     *
     * @param \Aheadworks\Blog\Model\Post $post
     * @return string
     */
    public function getItemHtml(\Aheadworks\Blog\Model\Post $post)
    {
        $html = '';
        /** @var \Aheadworks\Blog\Block\Post $block */
        $block = $this->getLayout()->createBlock('Aheadworks\Blog\Block\Post');
        if ($block) {
            $html = $block
                ->setMode(\Aheadworks\Blog\Block\Post::MODE_LIST_ITEM)
                ->setPostModel($post)
                ->toHtml();
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getPostCollection() ? $this->getChildHtml('pager') : '';
    }
}
