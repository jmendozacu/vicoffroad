<?php
namespace Aheadworks\Blog\Helper;

/**
 * Sitemap helper
 */
class Sitemap extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var Url
     */
    protected $urlHelper;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Config $configHelper
     * @param Url $urlHelper
     * @param \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Config $configHelper,
        Url $urlHelper,
        \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
    ) {
        parent::__construct($context);
        $this->configHelper = $configHelper;
        $this->urlHelper = $urlHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->postCollectionFactory = $postCollectionFactory;
    }

    /**
     * Retrieves blog home page sitemap item
     *
     * @param int $storeId
     * @return \Magento\Framework\DataObject
     */
    public function getBlogItem($storeId)
    {
        return new \Magento\Framework\DataObject(
            [
                'changefreq' => $this->getChangeFreq($storeId),
                'priority' => $this->getPriority($storeId),
                'collection' => [
                    new \Magento\Framework\DataObject(
                        [
                            'id' => 'blog_home',
                            'url' => $this->configHelper->getValue(Config::XML_GENERAL_ROUTE_TO_BLOG, $storeId),
                            'updated_at' => $this->getCurrentDateTime()
                        ]
                    )
                ]
            ]
        );
    }

    /**
     * Retrieves category sitemap items
     *
     * @param int $storeId
     * @return \Magento\Framework\DataObject
     */
    public function getCategoryItems($storeId)
    {
        $categoryItems = [];
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addEnabledFilter()
            ->addStoreFilter($storeId);
        foreach ($categoryCollection as $category) {
            $categoryItems[$category->getId()] = new \Magento\Framework\DataObject(
                [
                    'id' => $category->getId(),
                    'url' => $this->urlHelper->getCategoryRoute($category),
                    'updated_at' => $this->getCurrentDateTime()
                ]
            );
        }
        return new \Magento\Framework\DataObject(
            [
                'changefreq' => $this->getChangeFreq($storeId),
                'priority' => $this->getPriority($storeId),
                'collection' => $categoryItems
            ]
        );
    }

    /**
     * Retrieves post sitemap items
     *
     * @param int $storeId
     * @return \Magento\Framework\DataObject
     */
    public function getPostItems($storeId)
    {
        $postItems = [];
        $postCollection = $this->postCollectionFactory->create()
            ->addPublishedFilter()
            ->addStoreFilter($storeId);
        foreach ($postCollection as $post) {
            $postItems[$post->getId()] = new \Magento\Framework\DataObject(
                [
                    'id' => $post->getId(),
                    'url' => $this->urlHelper->getPostRoute($post),
                    'updated_at' => $this->getCurrentDateTime()
                ]
            );
        }
        return new \Magento\Framework\DataObject(
            [
                'changefreq' => $this->getChangeFreq($storeId),
                'priority' => $this->getPriority($storeId),
                'collection' => $postItems
            ]
        );
    }

    /**
     * @param int $storeId
     * @return float
     */
    protected function getChangeFreq($storeId)
    {
        return $this->configHelper->getValue(Config::XML_SITEMAP_CHANGEFREQ, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    protected function getPriority($storeId)
    {
        return $this->configHelper->getValue(Config::XML_SITEMAP_PRIORITY, $storeId);
    }

    /**
     * Current date/time
     *
     * @return string
     */
    protected function getCurrentDateTime()
    {
        return (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
    }
}
