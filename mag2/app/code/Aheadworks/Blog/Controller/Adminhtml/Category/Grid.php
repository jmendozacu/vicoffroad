<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Category;

class Grid extends \Aheadworks\Blog\Controller\Adminhtml\Category
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->_getResultPage();
    }
}