<?php
namespace Smv\Ebaygallery\Block\Adminhtml\Photogallery\Edit\Tab;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_objectManager;

    protected $_helper;

    protected $_customergroup;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Smv\Ebaygallery\Helper\Data $helper,
        \Magento\Customer\Model\Group $customer_group,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    )
    {
        $this->_systemStore = $systemStore;
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
        $this->_customergroup = $customer_group;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('photogallery_data');

        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('photogallery_fieldset', array('legend' => __('Gallery Information')));

        if ($model->getId()) {
            $fieldset->addField('photogallery_id', 'hidden', array('name' => 'photogallery_id'));
        }


        $fieldset->addField('gal_name', 'text', array(
            'label' => __('Photogallery Name'),
            'required' => true,
            'name' => 'gal_name',
        ));
        $fieldset->addField('gorder', 'text', array(
            'label' => __('Position'),
            'required' => false,
            'name' => 'gorder',
        ));
        $fieldset->addField('show_in', 'select', array(
            'label' => __('Gallery Type'),
            'name' => 'show_in',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => __('Banner'),
                ),
                array(
                    'value' => 2,
                    'label' => __('Category box'),
                ),
                array(
                    'value' => 3,
                    'label' => __('Cross Seller'),
                ),
                array(
                    'value' => 4,
                    'label' => __('Brand'),
                ),
            ),
        ));


        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Photogallery Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Photogallery Information');
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
