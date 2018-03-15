<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit;

use \Aheadworks\Blog\Helper\Config;
use Aheadworks\Blog\Model\Source\Post\Status;

/**
 * Edit blog post
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @var \Aheadworks\Blog\Model\Source\Post\Status
     */
    protected $statusSource;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Aheadworks\Blog\Helper\Config
     */
    protected $configHelper;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Aheadworks\Blog\Model\ResourceModel\Category\Collection $categoryCollection
     * @param Status $statusSource
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param Config $configHelper
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Aheadworks\Blog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Aheadworks\Blog\Model\Source\Post\Status $statusSource,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Store\Model\System\Store $systemStore,
        \Aheadworks\Blog\Helper\Config $configHelper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->categoryCollection = $categoryCollection;
        $this->authSession = $authSession;
        $this->statusSource = $statusSource;
        $this->systemStore = $systemStore;
        $this->configHelper = $configHelper;
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $post = $this->_coreRegistry->registry('aw_blog_post');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('general_fieldset', []);

        if ($post->getPostId()) {
            $fieldset->addField('post_id', 'hidden', ['name' => 'post_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            ['name' => 'title', 'label' => __('Title'), 'title' => __('Title'), 'required' => true]
        );
        $fieldset->addField(
            'url_key',
            'text',
            ['name' => 'url_key', 'label' => __('URL-Key'), 'title' => __('URL-Key'), 'required' => true]
        );
        $fieldset->addField(
            'has_short_content',
            'checkbox',
            [
                'name'  => 'has_short_content',
                'label' => __('Short Content'),
                'checked' => $post->getShortContent(),
                'onchange' => 'hideShowShortContent(this);'
            ]
        );
        $fieldset->addField(
            'short_content',
            'editor',
            [
                'name' => 'short_content',
                'label' => __('Short Content'),
                'title' => __('Short Content'),
                'style' => 'height:6em',
                'required' => true,
                'config' => $this->wysiwygConfig->getConfig()
            ]
        );
        $fieldset->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:36em',
                'required' => true,
                'config' => $this->wysiwygConfig->getConfig()
            ]
        );
        //todo: uncomment this when http://issues.aheadworks.com/browse/MMBLOG-38 is fixed
//        $metaTitleLength = strlen($post->getMetaTitle());
//        $fieldset->addField(
//            'meta_title',
//            'text',
//            [
//                'name' => 'meta_title',
//                'label' => __('Meta Title'),
//                'title' => __('Meta Title'),
//                'required' => true,
//                'after_element_html' => $this->getCharCountJs('meta_title', 60),
//                'note' => "<span class='char-count'>{$metaTitleLength}</span>" .
//                    __(" characters used. Recommended max length is 50-60 characters")
//            ]
//        );
        $metaDescLength = strlen($post->getMetaDescription());
        $fieldset->addField(
            'meta_description',
            'text',
            [
                'name' => 'meta_description',
                'label' => __('Meta Description'),
                'title' => __('Meta Description'),
                'required' => true,
                'after_element_html' => $this->getCharCountJs('meta_description', 160),
                'note' => "<span class='char-count'>{$metaDescLength}</span>" .
                    __(" characters used. Recommended max length is 150-160 characters")
            ]
        );

        $fieldset = $form->addFieldset('settings_fieldset', []);
        $post->setStatusText($this->statusSource->getOptionLabelByValue($post->getVirtualStatus()));
        $fieldset->addField(
            'status_text',
            'label',
            [
                'name' => 'status_text',
                'label' => __('Status:'),
                'title' => __('Status:'),
                'css_class' => $post->getVirtualStatus()
            ]
        );
        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::MEDIUM
        );
        if ($post->getVirtualStatus() == Status::PUBLICATION_PUBLISHED) {
            if ($this->authSession->isAllowed('Aheadworks_Blog::comments')) {
                $fieldset->addField(
                    'comments_link',
                    'Aheadworks\Blog\Block\Adminhtml\Post\Edit\Form\Element\CommentsLink',
                    ['name' => 'comments_link']
                );
            }
            $fieldset->addField(
                'publish_date',
                'date',
                [
                    'name' => 'publish_date',
                    'label' => __('Published At'),
                    'disabled' => true,
                    'date_format' => $dateFormat,
                    'time_format' => $timeFormat
                ]
            );
        } else {
            $fieldset->addField(
                'is_scheduled',
                'checkbox',
                [
                    'name'  => 'is_scheduled',
                    'value'  => 'is_scheduled',
                    'label' => __('Schedule Post'),
                    'checked' => $post->getIsScheduled() || $post->getVirtualStatus() == Status::PUBLICATION_SCHEDULED,
                    'onchange' => 'hideShowSchedule(this);'
                ]
            );
            $fieldset->addField(
                'publish_date',
                'date',
                [
                    'name' => 'publish_date',
                    'required' => true, //todo 'required field' message not displayed
                    'date_format' => $dateFormat,
                    'time_format' => $timeFormat
                ]
            );
        }
        $fieldset->addField(
            'categories',
            'multiselect',
            [
                'name' => 'categories',
                'label' => __('Categories'),
                'title' => __('Categories'),
                'values' => $this->categoryCollection->toOptionArray(),
                'class' => 'select'
            ]
        );
        $fieldset->addField(
            'tags',
            'multiselect',
            [
                'name' => 'tags',
                'label' => __('Tags'),
                'title' => __('Tags'),
                'values' => $post->getTagsAsOptionArray()
            ]
        );
        /* Check if store has only one store view */
        if (!$this->_storeManager->hasSingleStore()) {
            $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'name' => 'stores',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                    'value' => 0
                ]
            );
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $post->setStores($this->_storeManager->getStore(true)->getId());
        }
        if ($this->configHelper->getValue(Config::XML_GENERAL_DISQUS_FORUM_CODE)) {
            $fieldset->addField(
                'is_allow_comments',
                'select',
                [
                    'name' => 'is_allow_comments',
                    'label' => __('Allow Comments'),
                    'title' => __('Allow Comments'),
                    'values' => [0 => __('No'), 1 => __('Yes')],
                    'class' => 'select'
                ]
            );
        }

        $form->addValues($post->getData());
        if ($post->getPublishDate()) {
            $localPublishDate = $this->_localeDate->date($post->getPublishDate())->format('Y-m-d H:i:s');
            $form->addValues(['publish_date' => $localPublishDate]);
        }
        if (is_array($post->getTags())) {
            $form->addValues(['tags' => $post->getTags()]);
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param string $inputId
     * @param int $warningLength
     * @return string
     */
    protected function getCharCountJs($inputId, $warningLength)
    {
        $options =  \Zend_Json::encode(
            [
                'warningLength' => $warningLength,
                'noteElement' => "#{$inputId}-note",
                'charCountDestElement' => "#{$inputId}-note .char-count"
            ]
        );
        return <<<HTML
    <script>
        require(['jquery', 'mage/mage'], function($) {
            $(document).ready(function () {
                $("#{$inputId}").mage('blogPostMetaCharCount', {$options});
            });
        });
    </script>
HTML;
    }
}
