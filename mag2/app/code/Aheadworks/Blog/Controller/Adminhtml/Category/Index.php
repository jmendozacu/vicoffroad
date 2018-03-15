<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Category;

class Index extends \Aheadworks\Blog\Controller\Adminhtml\Category
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_getResultPage();
        $resultPage->setActiveMenu('Aheadworks_Blog::categories');
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));
        return $resultPage;
    }
}