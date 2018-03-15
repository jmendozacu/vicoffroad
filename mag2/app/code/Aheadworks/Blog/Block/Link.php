<?php
namespace Aheadworks\Blog\Block;

/**
 * Link to blog page
 * @package Aheadworks\Blog\Block
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * Get href URL
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getData('href');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '<a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a>';
    }
}
