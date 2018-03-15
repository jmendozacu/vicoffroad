<?php
namespace Aheadworks\Blog\Observer;

use Aheadworks\Blog\Model\Category;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddBlogToTopmenuItemsObserver
 * @package Aheadworks\Blog\Observer
 */
class AddBlogToTopmenuItemsObserver implements ObserverInterface
{
    const NODE_ID_PREFIX = 'aw-blog-menu-item-node';

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection|null
     */
    protected $categoryCollection = null;

    /**
     * @var \Aheadworks\Blog\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Aheadworks\Blog\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Aheadworks\Blog\Helper\Config $configHelper
     * @param \Aheadworks\Blog\Helper\Url $urlHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Aheadworks\Blog\Helper\Config $configHelper,
        \Aheadworks\Blog\Helper\Url $urlHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->configHelper = $configHelper;
        $this->urlHelper = $urlHelper;
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    /**
     * @return \Aheadworks\Blog\Model\ResourceModel\Category\Collection|null
     */
    protected function getCategoryCollection()
    {
        if ($this->categoryCollection === null) {
            $this->categoryCollection = $this->categoryCollectionFactory->create()
                ->addEnabledFilter()
                ->addStoreFilter($this->storeManager->getStore()->getId())
                ->setOrder('sort_order', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        }
        return $this->categoryCollection;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
//        $menuBlock = $observer->getEvent()->getBlock();
//        $menuBlock->addIdentity(Category::CACHE_TAG);
//
//        $blogHomeItem = $this->addItem($this->getMenuItemData(), $observer->getMenu(), $menuBlock);
//
//        foreach ($this->getCategoryCollection() as $category) {
//            //$this->addItem($this->getMenuItemData($category), $blogHomeItem, $menuBlock);
//        }
    }

    /**
     * @param array $itemData
     * @param \Magento\Framework\Data\Tree\Node $parentNode
     * @param \Magento\Theme\Block\Html\Topmenu $menuBlock
     * @return \Magento\Framework\Data\Tree\Node
     */
    protected function addItem($itemData, $parentNode, $menuBlock)
    {
        $menuBlock->addIdentity(Category::CACHE_TAG . '_' . $itemData['id']);
        $menuNode = new \Magento\Framework\Data\Tree\Node(
            $itemData,
            'id',
            $parentNode->getTree(),
            $parentNode
        );
        $parentNode->addChild($menuNode);
        return $menuNode;
    }

    /**
     * @param Category|null $category
     * @return array
     */
    protected function getMenuItemData($category = null)
    {
        if ($category instanceof Category) {
            $nodeId = self::NODE_ID_PREFIX . '-' . $category->getId();
            $name = $category->getName();
            $url = $this->urlHelper->getCategoryUrl($category);
            $hasActive = false;
            $isActive = $this->isCategoryActive($category);
        } else {
            $nodeId = self::NODE_ID_PREFIX;
            $name = $this->configHelper->getValue('aw_blog/general/blog_title');
            $url = $this->urlHelper->getBlogHomeUrl();
            $hasActive = $this->isBlogCategoryActive();
            $isActive = $this->isBlogHomeActive();
        }

        return [
            'id' => $nodeId,
            'name' => $name,
            'url' => $url,
            'has_active' => $hasActive,
            'is_active' => $isActive
        ];
    }

    /**
     * Checks whether the blog home item is active
     *
     * @return bool
     */
    protected function isBlogHomeActive()
    {
        return (bool)$this->coreRegistry->registry('aw_blog_action', true);
    }

    /**
     * Checks if any of blog categories is active
     *
     * @return bool
     */
    protected function isBlogCategoryActive()
    {
        foreach ($this->getCategoryCollection() as $category) {
            if ($this->isCategoryActive($category)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks whether the given category is active
     *
     * @param Category $category
     * @return bool
     */
    protected function isCategoryActive($category)
    {
        $currentViewCategory = $this->coreRegistry->registry('aw_blog_category');
        $currentPostCategoryId = $this->request->getParam('category_id');
        return ($currentViewCategory instanceof Category && $currentViewCategory->getId() == $category->getId()) ||
                ($currentPostCategoryId == $category->getId());
    }
}
