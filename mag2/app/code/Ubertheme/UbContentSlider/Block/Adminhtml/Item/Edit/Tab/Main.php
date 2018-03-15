<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Block\Adminhtml\Item\Edit\Tab;

/**
 * UBCS slide item edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Ubertheme\UbContentSlider\Model\Item */
        $model = $this->_coreRegistry->registry('ubcontentslider_item');
        
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ubertheme_UbContentSlider::item_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('item_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Basic Information')]);

        if ($model->getId()) {
            $fieldset->addField('item_id', 'hidden', ['name' => 'item_id']);
        }
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Slide Item Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'slide_id',
            'select',
            [
                'label' => __('Show In Slider'),
                'title' => __('Select One Slider'),
                'name' => 'slide_id',
                'required' => true,
                'options' => $model->getSliderOptions(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'link',
            'text',
            [
                'name' => 'link',
                'label' => __('Link'),
                'title' => __('Link of slide item'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'target',
            'select',
            [
                'name' => 'target',
                'label' => __('Link Target'),
                'title' => __('Select target of slide item\'s link'),
                'required' => true,
                'options' => $model->getLinkTargetOptions(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'content_type',
            'select',
            [
                'name' => 'content_type',
                'label' => __('Content Type'),
                'title' => __('Select content type of slide item'),
                'required' => true,
                'options' => $model->getContentTypeOptions(),
                'onchange' => 'showHideContentFields(this);',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'video_id',
            'text',
            [
                'name' => 'video_id',
                'label' => __('Video ID'),
                'title' => __('The ID of video (Support: Youtube and Vimeo video)'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'image',
            'image',
            array(
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('The Image to upload'),
                'note' => __('Allowed file types: jpg, jpeg, gif, png'),
                'required' => false,
                'disabled' => $isElementDisabled
            )
        );

        if($model->hasData('start_time')) {
            $datetime = new \DateTime($model->getData('start_time'));
            $model->setData('start_time', $datetime->setTimezone(new \DateTimeZone($this->_localeDate->getConfigTimezone())));
        }

        if($model->hasData('end_time')) {
            $datetime = new \DateTime($model->getData('end_time'));
            $model->setData('end_time', $datetime->setTimezone(new \DateTimeZone($this->_localeDate->getConfigTimezone())));
        }

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $timeFormat = $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT);
        $style = 'color: #000;background-color: #fff;font-weight:bold;font-size:13px;';
        $fieldset->addField(
            'start_time',
            'date',
            [
                'name' => 'start_time',
                'label' => __('Publish Time'),
                'title' => __('Starting publish time'),
                'required' => true,
                'readonly' => true,
                'style' => $style,
                'class' => 'required-entry',
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'note' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::SHORT),
            ]
        );
        $fieldset->addField(
            'end_time',
            'date',
            [
                'name' => 'end_time',
                'label' => __('End Time'),
                'title' => __('Ending publish time'),
                'required' => true,
                'readonly' => true,
                'style' => $style,
                'class' => 'required-entry',
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'note' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::SHORT)
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description Of Slide Item'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Slide Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'class' => 'validate-not-negative-number'
            ]
        );
        
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }
        
        if($model->getData('image')){
			$model->setData('image', $model->getData('image'));
        }

        $this->_eventManager->dispatch('adminhtml_ubcontentslider_item_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Basic Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Basic Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
