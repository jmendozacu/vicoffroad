<?php
namespace Smv\Ebaygallery\Controller\Adminhtml\Photogallery;

class Products extends \Smv\Ebaygallery\Controller\Adminhtml\Photogallery
{
    
    public function execute()
    {
    	//echo "this"; exit;
		$resultLayout = $this->resultLayoutFactory->create();
		$this->_initProductPhotogallery();
		
        $resultLayout->getLayout()->getBlock('photogallery.edit.tab.products')
                          ->setRelatedProducts($this->getRequest()->getPost('products_related', null));				  
        return $resultLayout;
    
    }
    
}
