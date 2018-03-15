<?php
namespace Aheadworks\Blog\Block\Adminhtml\Category;

/**
 * Edit category
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'cat_id';
        $this->_blockGroup = 'Aheadworks_Blog';
        $this->_controller = 'adminhtml_category';

        parent::_construct();

        $this->buttonList->remove('reset');
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            -1
        );
    }
}