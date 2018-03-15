<?php
namespace Aheadworks\Blog\Block\Adminhtml;

class Category extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Aheadworks_Blog';
        $this->_controller = 'adminhtml_category';
        $this->_headerText = __('Categories');
        parent::_construct();
        $this->updateButton('add', 'label', __('Create Category'));
    }
}
