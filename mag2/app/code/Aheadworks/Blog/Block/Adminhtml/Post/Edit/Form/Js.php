<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Form;

use Aheadworks\Blog\Model\Source\Post\Status;

class Js extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Check if post has "Scheduled" virtual status
     *
     * @return boolean
     */
    public function isScheduledPost()
    {
        $post = $this->coreRegistry->registry("aw_blog_post");
        return $post->getVirtualStatus() == Status::PUBLICATION_SCHEDULED;
    }

    /**
     * Get button label depending on post status
     *
     * @param bool $isScheduledPost
     * @return \Magento\Framework\Phrase
     */
    public function getSaveButtonLabel($isScheduledPost)
    {
        if ($isScheduledPost) {
            if ($this->isScheduledPost()) {
                $label = __("Save");
            } else {
                $label = __("Schedule Post");
            }
        } else {
            $label = __("Publish Post");
        }
        return $label;
    }
}
