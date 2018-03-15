<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;

class Save extends \Aheadworks\Blog\Controller\Adminhtml\Post
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Aheadworks\Blog\Model\PostFactory
     */
    protected $postFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Aheadworks\Blog\Model\PostFactory $postFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Aheadworks\Blog\Model\PostFactory $postFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->dateTime = $dateTime;
        $this->postFactory = $postFactory;
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
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var $post \Aheadworks\Blog\Model\Post */
            $post = $this->postFactory->create();
            $postId = $this->getRequest()->getParam('post_id');
            if (!$postId) {
                $data['author_id'] = $this->_auth->getUser()->getId();
                $data['author_name'] = $this->_auth->getUser()->getName();
            }
            if (!isset($data['short_content'])) {
                $data['short_content'] = "";
            }
            if (isset($data['publish_date'])) {
                // converting date from local to gmt to store in db. Is there a better solution?
                $gmtTimestamp = strtotime($data['publish_date']) - $this->dateTime->getGmtOffset();
                $data['publish_date'] = date('Y-m-d H:i:s', $gmtTimestamp);
            }
            if ($this->getRequest()->getParam('save_as_draft')) {
                $data['save_as_draft'] = true;
            }
            $post
                ->load($postId)
                ->addData($data)
            ;
            $back = $this->getRequest()->getParam('back');
            try {
                $post->save();
                $this->messageManager->addSuccess(__('Blog post was successfully saved.'));
                $this->_getSession()->setFormData(false);
                if ($back == 'edit') {
                    return $resultRedirect->setPath('*/*/' . $back, ['post_id' => $post->getPostId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the post.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $postId]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
