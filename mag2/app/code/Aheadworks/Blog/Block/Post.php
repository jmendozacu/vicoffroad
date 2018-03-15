<?php
namespace Aheadworks\Blog\Block;

use Aheadworks\Blog\Helper\Config;
use Aheadworks\Blog\Model\Source\Post\SharingButtons\DisplayAt as DisplaySharingAt;

/**
 * Post view/list item block
 *
 * @method bool hasPostModel()
 * @method bool hasMode()
 * @method \Aheadworks\Blog\Model\Post getPostModel()
 * @method string getMode()
 *
 * @method Post setPostModel(\Aheadworks\Blog\Model\Post $post)
 * @method Post setMode(string)
 *
 * @package Aheadworks\Blog\Block
 */
class Post extends \Aheadworks\Blog\Block\PostAbstract
{
    const MODE_LIST_ITEM = 'list_item';
    const MODE_VIEW = 'view';

    /**
     * @var string
     */
    protected $_template = 'post.phtml';

    /**
     * @var \Magento\Cms\Model\Template\Filter
     */
    protected $templateFilter;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection|null
     */
    protected $categoryCollection = null;

    /**
     * @var LinkFactory
     */
    protected $linkFactory;

    /**
     * Post constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\Filter $templateFilter
     * @param LinkFactory $linkFactory
     * @param \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Aheadworks\Blog\Helper\Url $urlHelper
     * @param Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\Filter $templateFilter,
        LinkFactory $linkFactory,
        \Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Aheadworks\Blog\Helper\Url $urlHelper,
        \Aheadworks\Blog\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $postCollectionFactory,
            $urlHelper,
            $configHelper,
            $data
        );
        $this->templateFilter = $templateFilter; // todo: filter provider - better solution
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->linkFactory = $linkFactory;
        if (!$this->hasPostModel()) {
            $this->setPostModel($this->getCurrentPostModel());
        }
        if (!$this->hasMode()) {
            $this->setMode(self::MODE_VIEW);
        }
    }

    /**
     * Check whether block in list item mode
     *
     * @return bool
     */
    public function isListItemMode()
    {
        return $this->getMode() == self::MODE_LIST_ITEM;
    }

    /**
     * Check whether block in view mode
     *
     * @return bool
     */
    public function isViewMode()
    {
        return $this->getMode() == self::MODE_VIEW;
    }

    /**
     * @param \Aheadworks\Blog\Model\Post $post
     * @return string
     */
    public function getContent(\Aheadworks\Blog\Model\Post $post)
    {
        $content = $post->getContent();
        if ($this->isListItemMode() && $post->getShortContent()) {
            $content = $post->getShortContent();
        }
        return $this->templateFilter
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->filter($content);
    }

    /**
     * @return bool
     */
    public function showSharing()
    {
        $config = explode(
            ',',
            $this->configHelper->getValue(Config::XML_GENERAL_DISPLAY_SHARING_AT)
        );
        return (
            $this->isListItemMode() && in_array(DisplaySharingAt::POST_LIST, $config) ||
            $this->isViewMode() && in_array(DisplaySharingAt::POST, $config)
        );
    }

    /**
     * Retrieves Sharethis embed code html
     *
     * @return string
     */
    public function getSharethisEmbedHtml()
    {
        $post = $this->getPostModel();
        $sharethisEmbed = $this->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Aheadworks_Blog::sharethis/buttons.phtml')
            ->setShareUrl($this->getPostUrl($post))
            ->setSharingText($post->getTitle());
        return $sharethisEmbed->toHtml();
    }

    /**
     * Retrieves Disqus embed code html
     *
     * @return string
     */
    public function getDisqusEmbedHtml()
    {
        $html = '';
        /** @var \Aheadworks\Blog\Block\Disqus $disqusEmbed */
        $disqusEmbed = $this->getChildBlock('disqus_embed');
        if ($disqusEmbed) {
            $post = $this->getPostModel();
            $html = $disqusEmbed
                ->setPageIdentifier($post->getId())
                ->setPageUrl($this->getPostUrl($post))
                ->setPageTitle($post->getTitle())
                ->toHtml();
        }
        return $html;
    }

    /**
     * @return \Aheadworks\Blog\Model\ResourceModel\Category\Collection
     */
    public function getCategoryCollection()
    {
        if ($this->categoryCollection === null) {
            $this->categoryCollection = $this->categoryCollectionFactory->create()
                ->addEnabledFilter()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addFieldToFilter('cat_id', ['in' => $this->getPostModel()->getCategories()]);
        }
        return $this->categoryCollection;
    }

    /**
     * @param \Aheadworks\Blog\Model\Category $category
     * @return string
     */
    public function getCategoryLinkHtml($category)
    {
        /** @var Link $link */
        $link = $this->linkFactory->create();
        return $link
            ->setHref($this->urlHelper->getCategoryUrl($category))
            ->setTitle($category->getName())
            ->setLabel($category->getName())
            ->toHtml();
    }

    /**
     * @return bool
     */
    public function commentsEnabled()
    {
        return $this->configHelper->getValue(Config::XML_GENERAL_DISQUS_FORUM_CODE) &&
            $this->getPostModel()->getIsAllowComments();
    }

    /**
     * @param \Aheadworks\Blog\Model\Post $post
     * @return bool
     */
    public function showReadMoreButton(\Aheadworks\Blog\Model\Post $post)
    {
        return $this->isListItemMode() && $post->getShortContent();
    }

    /**
     * @param string $tag
     * @return string
     */
    public function getSearchByTagUrl($tag)
    {
        return $this->urlHelper->getSearchByTagUrl($tag);
    }
}
