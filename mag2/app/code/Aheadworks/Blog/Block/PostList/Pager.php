<?php
namespace Aheadworks\Blog\Block\PostList;

/**
 * Pager block
 * @package Aheadworks\Blog\Block\PostList
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * Retrieves page URL
     *
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        return $this->getUrl(
            null,
            [
                '_current' => true,
                '_escape' => true,
                '_use_rewrite' => true,
                '_fragment' => $this->getFragment(),
                '_query' => $params,
                '_direct' => $this->getPath()
            ]
        );
    }
}
