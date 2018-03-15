<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;

class Edit extends \Aheadworks\Blog\Controller\Adminhtml\Post
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Aheadworks\Blog\Model\PostFactory
     */
    protected $postFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Aheadworks\Blog\Model\PostFactory $postFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Aheadworks\Blog\Model\PostFactory $postFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->postFactory = $postFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * Edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var $post \Aheadworks\Blog\Model\Post */
        $post = $this->postFactory->create();
        $postId = $this->getRequest()->getParam('post_id');
        if ($postId) {
            $post->load($postId);
            if (!$post->getId()) {
                $this->messageManager->addError(__('This post no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/*');
            }
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $post->addData($data);
        }

        $this->coreRegistry->register('aw_blog_post', $post);

        $resultPage = $this->_getResultPage();
        $resultPage->setActiveMenu('Aheadworks_Blog::posts');
        $resultPage->getConfig()->getTitle()->prepend(
            $post->getId() ?  __('Edit Post') : __('New Post')
        );

        return $resultPage;
    }
}
