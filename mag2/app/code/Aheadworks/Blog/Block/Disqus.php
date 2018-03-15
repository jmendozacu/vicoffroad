<?php
namespace Aheadworks\Blog\Block;

use Aheadworks\Blog\Helper\Config;

/**
 * Disqus integration block
 *
 * @method int getPageIdentifier()
 * @method string getPageUrl()
 * @method string getPageTitle()
 *
 * @method Disqus setPageIdentifier(int)
 * @method Disqus setPageUrl(string)
 * @method Disqus setPageTitle(string)
 *
 * @package Aheadworks\Blog\Block
 */
class Disqus extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Aheadworks\Blog\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aheadworks\Blog\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aheadworks\Blog\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    /**
     * @return bool
     */
    public function commentsEnabled()
    {
        return (bool)$this->getDisqusForumCode();
    }

    /**
     * @return string
     */
    public function getDisqusForumCode()
    {
        return $this->configHelper->getValue(Config::XML_GENERAL_DISQUS_FORUM_CODE);
    }
}
