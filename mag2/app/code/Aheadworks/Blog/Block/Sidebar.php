<?php
namespace Aheadworks\Blog\Block;

/**
 * Sidebar base class
 * @package Aheadworks\Blog\Block
 */
class Sidebar extends \Magento\Framework\View\Element\Template
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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aheadworks\Blog\Helper\Url $urlHelper
     * @param \Aheadworks\Blog\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aheadworks\Blog\Helper\Url $urlHelper,
        \Aheadworks\Blog\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlHelper = $urlHelper;
        $this->configHelper = $configHelper;
    }
}
