<?php
namespace Aheadworks\Blog\Block;

/**
 * Class PostAbstract
 * @package Aheadworks\Blog\Block
 */
abstract class PostAbstract extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Aheadworks\Blog\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var \Aheadworks\Blog\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Post\Collection|null
     */
    protected $postCollection = null;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var array
     */
    protected $postUrls = [];

    /**
     * PostAbstract constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Aheadworks\Blog\Helper\Url $urlHelper
     * @param \Aheadworks\Blog\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Aheadworks\Blog\Helper\Url $urlHelper,
        \Aheadworks\Blog\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->urlHelper = $urlHelper;
        $this->configHelper = $configHelper;
        $this->initPostCollection($postCollectionFactory);
    }

    /**
     * Init post collection
     *
     * @param \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $collectionFactory
     */
    protected function initPostCollection($collectionFactory)
    {
        $this->postCollection = $collectionFactory->create();
        $this->postCollection
            ->addPublishedFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_date', \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
        ;
        if ($currentCategory = $this->getCurrentCategoryModel()) {
            $this->postCollection->addCategoryFilter($currentCategory->getId());
        }
    }

    /**
     * @return \Aheadworks\Blog\Model\ResourceModel\Post\Collection
     */
    public function getPostCollection()
    {
        return $this->postCollection;
    }

    /**
     * Retrieves current post model
     *
     * @return \Aheadworks\Blog\Model\Post|null
     */
    public function getCurrentPostModel()
    {
        return $this->coreRegistry->registry('aw_blog_post');
    }

    /**
     * Retrieves current category model
     *
     * @return \Aheadworks\Blog\Model\Category|null
     */
    public function getCurrentCategoryModel()
    {
        return $this->coreRegistry->registry('aw_blog_category');
    }

    /**
     * Retrieves current tag model
     *
     * @return \Aheadworks\Blog\Model\Tag|null
     */
    public function getCurrentTagModel()
    {
        return $this->coreRegistry->registry('aw_blog_tag');
    }

    /**
     * @param \Aheadworks\Blog\Model\Post $post
     * @return string
     */
    public function getPostUrl(\Aheadworks\Blog\Model\Post $post)
    {
        $postId = $post->getId();
        if (!isset($this->postUrls[$postId])) {
            $this->postUrls[$postId] = $this->urlHelper->getPostUrl($post, $this->getCurrentCategoryModel());
        }
        return $this->postUrls[$postId];
    }
}
