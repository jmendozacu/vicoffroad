<?php
namespace Aheadworks\Blog\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Config helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_GENERAL_ROUTE_TO_BLOG = 'aw_blog/general/route_to_blog';
    const XML_GENERAL_BLOG_TITLE = 'aw_blog/general/blog_title';
    const XML_GENERAL_POSTS_PER_PAGE = 'aw_blog/general/posts_per_page';
    const XML_GENERAL_DISPLAY_SHARING_AT = 'aw_blog/general/display_sharing_buttons_at';
    const XML_GENERAL_DISQUS_FORUM_CODE = 'aw_blog/general/disqus_forum_code';
    const XML_GENERAL_DISQUS_SECRET_KEY = 'aw_blog/general/disqus_secret_key';
    const XML_SIDEBAR_RECENT_POSTS = 'aw_blog/sidebar/recent_posts';
    const XML_SIDEBAR_POPULAR_TAGS = 'aw_blog/sidebar/popular_tags';
    const XML_SIDEBAR_HIGHLIGHT_TAGS = 'aw_blog/sidebar/highlight_popular_tags';
    const XML_SIDEBAR_CMS_BLOCK = 'aw_blog/sidebar/cms_block';
    const XML_SEO_META_TITLE = 'aw_blog/seo/meta_title';
    const XML_SEO_META_DESCRIPTION = 'aw_blog/seo/meta_description';
    const XML_SEO_META_TAG_TEMPLATE = 'aw_blog/seo/post_meta_tag_template';
    const XML_SITEMAP_CHANGEFREQ = 'sitemap/aw_blog/changefreq';
    const XML_SITEMAP_PRIORITY = 'sitemap/aw_blog/priority';

    /**
     * @param string   $path
     * @param int|null $storeId
     * @param int|null $websiteId
     * @return mixed
     */
    public function getValue($path, $storeId = null, $websiteId = null)
    {
        if ($storeId !== null) {
            return $this->scopeConfig
                ->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
        } elseif ($websiteId !== null) {
            return $this->scopeConfig
                ->getValue($path, ScopeInterface::SCOPE_WEBSITE, $websiteId);
        } else {
            return $this->scopeConfig->getValue($path);
        }
    }
}
