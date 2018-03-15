<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

/**
 * Class Index
 * @package Aheadworks\Blog\Controller\Adminhtml\Post
 */
class Index extends \Aheadworks\Blog\Controller\Adminhtml\Post
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
        $resultPage->setActiveMenu('Aheadworks_Blog::posts');
        $resultPage->getConfig()->getTitle()->prepend(__('Posts'));
        return $resultPage;
    }
}