<?php
namespace Smv\Ebaygallery\Block\Adminhtml;

class Photogallery extends \Magento\Backend\Block\Widget\Grid\Container
{
    
  	/**
     * Function -> Constructor
    */

	public function _construct()
	{
	    $this->_controller = 'adminhtml_photogallery';
	    $this->_blockGroup = 'Smv_Ebaygallery';
	    $this->_headerText = __('Photo Gallery Manager');
	    $this->_addButtonLabel = __('Add Photo Gallery');
	    parent::_construct();
	}

}
