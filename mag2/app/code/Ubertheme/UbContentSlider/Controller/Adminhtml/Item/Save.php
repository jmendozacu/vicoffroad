<?php
/**
 *
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Ubertheme_UbContentSlider::item_save';
    
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context, PostDataProcessor $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($data) {
            //$data = $this->dataProcessor->filter($data);
            
            $model = $this->_objectManager->create('Ubertheme\UbContentSlider\Model\Item');
            
            //image process
             //if delete image checked
            if (isset($data['image']['delete']) AND $data['image']['delete']) {
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
                $imagePath = $mediaDirectory->getAbsolutePath($data['image']['value']);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $data['image']['value'] = null;
            }
            //if have new upload    
            if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
				try {
                    $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'image']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                    $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
                    $uploader->addValidateCallback('ubcontentslider_item_image', $imageAdapter, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath('ubcontentslider/images'));
                    unset($result['tmp_name']);
                    unset($result['path']);
                    $data['image'] = '/ubcontentslider/images'.$result['file'];
				} catch (\Exception $e) {
					$data['image'] = $_FILES['image']['name'];
                    $message = __('Something went wrong while uploading the image of slide item') . ": {$e->getMessage()}";
                    $this->messageManager->addException($e, $message);
				}
			}
			else{
                if (isset($data['image'])){
                    $data['image'] = $data['image']['value'];
                }
			}
            //end image process

            $id = $this->getRequest()->getParam('item_id');
            if ($id) {
                $model->load($id);
            }
            
            //set new data
            $model->setData($data);
            
            $this->_eventManager->dispatch(
                'ubcontentslider_item_prepare_save',
                ['item' => $model, 'request' => $this->getRequest()]
            );

//            if (!$this->dataProcessor->validate($data)) {
//                return $resultRedirect->setPath('*/*/edit', ['item_id' => $model->getId(), '_current' => true]);
//            }

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this slide item.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['item_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/', ['slide_id' => $model->getSlideId()]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the slide item information.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['item_id' => $this->getRequest()->getParam('item_id')]);
        }
        return $resultRedirect->setPath('*/*/', ['slide_id' => $data['slide_id']]);
    }
}
