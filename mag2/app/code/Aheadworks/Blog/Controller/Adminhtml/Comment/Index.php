<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Comment;

/**
 * Class Index
 * @package Aheadworks\Blog\Controller\Adminhtml\Comment
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Aheadworks\Blog\Helper\Disqus
     */
    protected $disqusHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Aheadworks\Blog\Helper\Disqus $disqusHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Aheadworks\Blog\Helper\Disqus $disqusHelper
    ) {
        parent::__construct($context);
        $this->disqusHelper = $disqusHelper;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aheadworks_Blog::comments');
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->disqusHelper->getAdminUrl());
        return $resultRedirect;
    }
}