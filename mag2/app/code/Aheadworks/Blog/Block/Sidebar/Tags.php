<?php
namespace Aheadworks\Blog\Block\Sidebar;

use Aheadworks\Blog\Helper\Config;

/**
 * Sidebar Tags block
 * @package Aheadworks\Blog\Block\Sidebar
 */
class Tags extends \Aheadworks\Blog\Block\Sidebar
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection
     */
    protected $tagCollection;

    /**
     * @var int
     */
    protected $minCount = 0;

    /**
     * @var int
     */
    protected $maxCount = 0;

    /**
     * @var float
     */
    protected $minWeight = 0.72;

    /**
     * @var float
     */
    protected $maxWeight = 1.28;

    /**
     * @var float
     */
    protected $slope = 0.1;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aheadworks\Blog\Helper\Url $urlHelper
     * @param \Aheadworks\Blog\Helper\Config $configHelper
     * @param \Aheadworks\Blog\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aheadworks\Blog\Helper\Url $urlHelper,
        \Aheadworks\Blog\Helper\Config $configHelper,
        \Aheadworks\Blog\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlHelper,
            $configHelper,
            $data
        );
        $this->coreRegistry = $coreRegistry;
        $this->initTagCollection($tagCollectionFactory);
    }

    /**
     * Init tag collection
     *
     * @param \Aheadworks\Blog\Model\ResourceModel\Tag\CollectionFactory $collectionFactory
     */
    protected function initTagCollection($collectionFactory)
    {
        $size = $this->configHelper->getValue(Config::XML_SIDEBAR_POPULAR_TAGS);
        $popularTagCollection = $collectionFactory->create();
        $popularTagCollection
            ->addFieldToFilter('count', ['gt' => 0])
            ->setOrder('count', \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
            ->setPageSize($size)
        ;
        if ($popularTagCollection->getSize()) {
            $this->maxCount = $popularTagCollection->getFirstItem()->getCount();
            $this->minCount = $popularTagCollection->getLastItem()->getCount();
        }
        $this->tagCollection = $collectionFactory->create();
        if ($this->minCount) {
            $this->tagCollection->addFieldToFilter('count', ['gteq' => $this->minCount]);
        }
        if ($currentCategory = $this->coreRegistry->registry('aw_blog_category')) {
            $this->tagCollection->addCategoryFilter($currentCategory->getId());
        }
        $this->tagCollection
            ->addPostsVisibilityFilter($this->_storeManager->getStore()->getId())
            ->setPageSize($size);
    }

    /**
     * @return \Aheadworks\Blog\Model\ResourceModel\Tag\Collection
     */
    public function getTagsCollection()
    {
        return $this->tagCollection;
    }

    /**
     * @return bool
     */
    public function isCloud()
    {
        return (bool)$this->configHelper->getValue(Config::XML_SIDEBAR_HIGHLIGHT_TAGS);
    }

    /**
     * @param \Aheadworks\Blog\Model\Tag $tag
     * @return int
     */
    public function getTagWeight(\Aheadworks\Blog\Model\Tag $tag)
    {
        $count = $tag->getCount();
        $averageCount = (int)($this->maxCount + $this->minCount) / 2;

        $weightOffset = $count >= $averageCount ? $this->maxWeight : $this->minWeight;
        $countOffset = $averageCount - $this->slope / ($weightOffset - 1);
        $weight = $weightOffset - $this->slope / ($count - $countOffset);

        return round($weight, 2) * 100;
    }

    /**
     * @param \Aheadworks\Blog\Model\Tag $tag
     * @return string
     */
    public function getSearchByTagUrl(\Aheadworks\Blog\Model\Tag $tag)
    {
        return $this->urlHelper->getSearchByTagUrl($tag);
    }
}
