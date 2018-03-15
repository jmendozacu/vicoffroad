<?php
namespace Smv\Ebaygallery\Controller\Index;

class Index extends \Smv\Ebaygallery\Controller\Index
{
    
    public function execute()
    {
		
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Photogallery'));
        return $resultPage;
        
    
    }
    
}
