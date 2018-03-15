<?php
namespace Aheadworks\Blog\Helper;

use Aheadworks\Blog\Controller\Router;
use Aheadworks\Blog\Helper\Config;
use Magento\Store\Model\ScopeInterface;

/**
 * Url helper
 * @package Aheadworks\Blog\Helper
 */
class Url extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Config $configHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Config $configHelper
    ) {
        parent::__construct($context);
        $this->configHelper = $configHelper;
    }

    /**
     * Retrieves blog home url
     *
     * @return string
     */
    public function getBlogHomeUrl()
    {
        return $this->_getUrl(null, ['_direct' => $this->getRouteToBlog()]);
    }

    /**
     * @param \Aheadworks\Blog\Model\Post $post
     * @param null $category
     * @return string
     */
    public function getPostUrl(\Aheadworks\Blog\Model\Post $post, $category = null)
    {
        $parts = [$this->getRouteToBlog()];
        if ($category instanceof \Aheadworks\Blog\Model\Category) {
            $parts[] = $category->getUrlKey();
        }
        $parts[] = $post->getUrlKey();
        return $this->_getUrl(null, ['_direct' => implode('/', $parts)]);
    }

    /**
     * @param \Aheadworks\Blog\Model\Post $post
     * @return string
     */
    public function getPostRoute(\Aheadworks\Blog\Model\Post $post)
    {
        return $this->getRouteToBlog() . '/' . $post->getUrlKey();
    }

    /**
     * @param \Aheadworks\Blog\Model\Category $category
     * @return string
     */
    public function getCategoryUrl(\Aheadworks\Blog\Model\Category $category)
    {
        return $this->_getUrl(null, ['_direct' => $this->getCategoryRoute($category)]);
    }

    /**
     * @param \Aheadworks\Blog\Model\Category $category
     * @return string
     */
    public function getCategoryRoute(\Aheadworks\Blog\Model\Category $category)
    {
        return $this->getRouteToBlog() . '/' . $category->getUrlKey();
    }

    /**
     * @param \Aheadworks\Blog\Model\Tag|string $tag
     * @return string
     */
    public function getSearchByTagUrl($tag)
    {
        $tagName = $tag instanceof \Aheadworks\Blog\Model\Tag ? $tag->getName() : $tag;
        return $this->_getUrl(
            null,
            ['_direct' => $this->getRouteToBlog() . '/' . Router::TAG_KEY . '/' . urlencode($tagName)]
        );
    }

    /**
     * @return string
     */
    protected function getRouteToBlog()
    {
        return $this->configHelper->getValue(Config::XML_GENERAL_ROUTE_TO_BLOG);
    }
}
