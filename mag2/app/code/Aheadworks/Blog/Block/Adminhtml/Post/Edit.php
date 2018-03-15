<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post;

use Aheadworks\Blog\Model\Source\Post\Status;

/**
 * Edit blog post
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
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
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'post_id';
        $this->_blockGroup = 'Aheadworks_Blog';
        $this->_controller = 'adminhtml_post';

        parent::_construct();

        $this->buttonList->remove('reset');

        /* @var $post \Aheadworks\Blog\Model\Post */
        $post = $this->coreRegistry->registry('aw_blog_post');

        if ($post->getStatus() == Status::PUBLICATION) {
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'class' => 'save',
                    'label' => __('Save and Continue Edit'),
                    'data_attribute' => [
                        'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                    ]
                ],
                1
            );
        } else {
            $this->buttonList->update('save', 'label', __("Publish Post"));
        }
        $this->buttonList->add(
            'saveasdraft',
            [
                'label' => __('Save as Draft'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form',
                            'eventData' => ['action' => ['args' => [ 'save_as_draft' => true]]],
                        ],
                    ],
                ]
            ],
            -1
        );
    }

    /**
     * @return string
     */
    public function getFormInitScripts()
    {
        $placeHolder = __('start typing to search tag');
        return <<<HTML
    <script>
        require(['jquery', 'jquerytokenize'], function($){
            $(document).ready(function () {
                $('#tags').tokenize();
            });
        });
    </script>
HTML;
    }
}
