<?php
namespace Smv\Ebaygallery\Controller\Adminhtml ;

abstract class Photogallery extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */

   
    protected $resultPageFactory;
    protected $resultLayoutFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
    }
       


	protected function _initAction() {
		$resultPage = $this->resultPageFactory->create();
		
		return $resultPage;
	}
	

	protected function _initProductPhotogallery() {
		
		
		$photogallery = $this->_objectManager->create('Smv\Ebaygallery\Model\Photogallery');
        $photogalleryId  = (int) $this->getRequest()->getParam('id');
       
		if ($photogalleryId) {
        	$photogallery->load($photogalleryId);
		} 
		$this->_objectManager->get('Magento\Framework\Registry')->register('current_photogallery_products', $photogallery);
        
		return $photogallery;
		
	}
    

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'photogallery; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
        
    
	
	protected function uploadFile( $file_name ){

        if( !empty($_FILES[$file_name]['name']) ){
            $result = array();
            $dynamicScmsURL = 'photogallery' . DS . 'files';
            $baseScmsMediaURL = $this->_storeManager->getStore()->getBaseUrl('') . DS . 'photogallery' . DS . 'files';
            $baseScmsMediaPath = Mage::getBaseDir('media') . DS .  'photogallery' . DS . 'files';
            
						
            $uploader = new Varien_File_Uploader( $file_name );
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','pdf','xls','xlsx','doc','docx','zip','ppt','pptx','flv','mp3','mp4','csv','html','bmp','txt','rtf','psd'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save( $baseScmsMediaPath );
       
            $file = str_replace(DS, '/', $result['file']);
            if( substr($baseScmsMediaURL, strlen($baseScmsMediaURL)-1)=='/' && substr($file, 0, 1)=='/' )    $file = substr($file, 1);
						
            $ScmsMediaUrl = $dynamicScmsURL.$file;
            
            $result['fieldname'] = $file_name;
            $result['url'] = $ScmsMediaUrl;
            $result['file'] = $result['file'];
            return $result;
        } else {
            return false;
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
