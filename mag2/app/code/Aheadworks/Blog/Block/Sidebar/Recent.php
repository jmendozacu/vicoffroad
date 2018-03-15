<?php
namespace Aheadworks\Blog\Block\Sidebar;

use Aheadworks\Blog\Helper\Config;

/**
 * Sidebar Recent posts block
 * @package Aheadworks\Blog\Block\Sidebar
 */
class Recent extends \Aheadworks\Blog\Block\PostAbstract
{
    /**
     * Init post collection
     *
     * @param \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $collectionFactory
     */
    protected function initPostCollection($collectionFactory)
    {
        parent::initPostCollection($collectionFactory);
        $this->postCollection->setPageSize(
            $this->configHelper->getValue(Config::XML_SIDEBAR_RECENT_POSTS)
        );
        if ($currentPost = $this->getCurrentPostModel()) {
            $this->postCollection->addFieldToFilter('post_id', ['neq' => $currentPost->getId()]);
        }
    }
}
