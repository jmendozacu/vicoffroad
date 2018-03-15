<?php
namespace Aheadworks\Blog\Block\Adminhtml\Category\Edit;

/**
 * Edit category
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

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
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $category = $this->_coreRegistry->registry('aw_blog_category');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('general_fieldset', []);

        if ($category->getCatId()) {
            $fieldset->addField('cat_id', 'hidden', ['name' => 'cat_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );
        $fieldset->addField(
            'url_key',
            'text',
            ['name' => 'url_key', 'label' => __('URL-Key'), 'title' => __('URL-Key'), 'required' => true]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => [0 => __('Disabled'), 1 => __('Enabled')],
                'class' => 'select'
            ]
        );
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
                'class' => 'validate-not-negative-number'
            ]
        );
//        todo: uncomment this when http://issues.aheadworks.com/browse/MMBLOG-38 is fixed
//        $fieldset->addField(
//            'meta_title',
//            'text',
//            [
//                'name' => 'meta_title',
//                'label' => __('Meta Title'),
//                'title' => __('Meta Title'),
//            ]
//        );
        $fieldset->addField(
            'meta_description',
            'editor',
            [
                'name' => 'meta_description',
                'label' => __('Meta Description'),
                'title' => __('Meta Description'),
                'style' => 'height:6em',
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
            $category->setStores($this->_storeManager->getStore(true)->getId());
        }

        $form->addValues($category->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
