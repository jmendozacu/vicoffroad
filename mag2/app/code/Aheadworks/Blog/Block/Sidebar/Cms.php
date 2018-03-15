<?php
namespace Aheadworks\Blog\Block\Sidebar;

use Aheadworks\Blog\Helper\Config;

/**
 * Sidebar Cms block
 * @package Aheadworks\Blog\Block\Sidebar
 */
class Cms extends \Aheadworks\Blog\Block\Sidebar
{
    /**
     * @var \Aheadworks\Blog\Helper\CmsBlock
     */
    protected $cmsBlockHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aheadworks\Blog\Helper\Url $urlHelper
     * @param Config $configHelper
     * @param \Aheadworks\Blog\Helper\CmsBlock $cmsBlockHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aheadworks\Blog\Helper\Url $urlHelper,
        \Aheadworks\Blog\Helper\Config $configHelper,
        \Aheadworks\Blog\Helper\CmsBlock $cmsBlockHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlHelper,
            $configHelper,
            $data
        );
        $this->cmsBlockHelper = $cmsBlockHelper;
    }

    /**
     * @return bool|\Magento\Cms\Model\Block
     */
    public function getCmsBlock()
    {
        return $this->cmsBlockHelper->getBlock(Config::XML_SIDEBAR_CMS_BLOCK);
    }

    /**
     * @param \Magento\Cms\Model\Block $cmsBlock
     * @return string
     */
    public function getCmsBlockHtml(\Magento\Cms\Model\Block $cmsBlock)
    {
        return $this->cmsBlockHelper->filter($cmsBlock->getContent());
    }
}
