<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;

class Delete extends \Aheadworks\Blog\Controller\Adminhtml\Post
{
    /**
     * @var \Aheadworks\Blog\Model\PostFactory
     */
    protected $postFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Aheadworks\Blog\Model\PostFactory $postFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Aheadworks\Blog\Model\PostFactory $postFactory
    ) {
        $this->postFactory = $postFactory;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $postId = $this->getRequest()->getParam('post_id');
        if ($postId) {
            /** @var $post \Aheadworks\Blog\Model\Post */
            $post = $this->postFactory->create()->load($postId);
            try {
                $post->delete();
                $this->messageManager->addSuccess(__('Post was successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while deleting the post.'));
            }
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $post->getPostId()]);
        }
        $this->messageManager->addError(__('Cannot delete: wrong post ID.'));
        return $resultRedirect->setPath('*/*/');
    }
}
