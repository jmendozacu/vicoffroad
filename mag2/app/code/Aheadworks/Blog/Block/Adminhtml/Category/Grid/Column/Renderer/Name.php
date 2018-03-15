<?php
namespace Aheadworks\Blog\Block\Adminhtml\Category\Grid\Column\Renderer;

/**
 * @package Aheadworks\Blog\Block\Adminhtml\Category\Grid\Column\Renderer
 */
class Name extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $attributes = new \Magento\Framework\DataObject([
            'href' => $this->getUrl('*/*/edit', ['cat_id' => $row->getCatId()])
        ]);
        return '<a ' . $attributes->serialize() . ' >' . $this->escapeHtml($row->getName()) . '</a>';
    }
}
