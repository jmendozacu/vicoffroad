<?php
namespace Smv\Ebaygallery\Controller\Adminhtml\Photogallery;

class Index extends \Smv\Ebaygallery\Controller\Adminhtml\Photogallery
{
    
    public function execute()
    {
		
		$resultPage = $this->resultPageFactory->create();
         $resultPage->setActiveMenu('Smv_Ebaygallery::manage_items');
        $resultPage->addBreadcrumb(__('Photogallery'), __('Photo Gallery'));
        $resultPage->addBreadcrumb(__('Photo Gallery'), __('Manage Photogallery'));
        $resultPage->getConfig()->getTitle()->prepend(__('Photo Gallery'));
        return $resultPage;
			
	
    }
}
