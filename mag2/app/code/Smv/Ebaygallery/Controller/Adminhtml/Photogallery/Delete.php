<?php
namespace Smv\Ebaygallery\Controller\Adminhtml\Photogallery;
use Magento\Framework\App\Filesystem\DirectoryList;
class Delete extends \Smv\Ebaygallery\Controller\Adminhtml\Photogallery
{
   
    public function execute()
    {
		$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
       $config = $this->_objectManager->get('Smv\Ebaygallery\Model\Media\Config');
       $mediaRootDir = $mediaDirectory->getAbsolutePath($config->getBaseTmpMediaPath());
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = $this->_objectManager->create('Smv\Ebaygallery\Model\Photogallery');
				/*Delete Images*/
				$object = $this->_objectManager->create('Smv\Ebaygallery\Model\ImgFactory');
				$collection = $object->create()->getCollection()->addFieldToFilter('photogallery_id',$this->getRequest()->getParam('id'));

				foreach ($collection as $col) {
					$file_name = $col->getImgName();
					$imgPath=  $this->splitImageValue($file_name,"path");
		            $imgName=  $this->splitImageValue($file_name,"name");
		            $file_path = $mediaRootDir . $file_name;
		            $thumb_path = $mediaRootDir .$imgPath. DIRECTORY_SEPARATOR.'thumb'.DIRECTORY_SEPARATOR.$imgName;
		            if ($file_path) {       
		                unlink($file_path); 
		                unlink($thumb_path);
		            }   

		        }

				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				$this->messageManager->addSuccess(__('Gallery was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				$this->messageManager->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	
    }
    
}
