<?php
namespace Smv\Ebaygallery\Controller\Adminhtml\Photogallery;

use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Smv\Ebaygallery\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->_imageFactory = $imageFactory;
        $this->_helper = $helper;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'image']
            );
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
            $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            $config = $this->_objectManager->get('Smv\Ebaygallery\Model\Media\Config');
            $result = $uploader->save($mediaDirectory->getAbsolutePath($config->getBaseTmpMediaPath()));

            $this->_eventManager->dispatch(
                'catalog_product_gallery_upload_image_after',
                ['result' => $result, 'action' => $this]
            );

            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = $this->_objectManager->get('Smv\Ebaygallery\Model\Media\Config')
                ->getTmpMediaUrl($result['file']);
             $fileName =  $result['file'];
            $result['file'] = $result['file'] . '.tmp';
           
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        // create thumbnail
        
        $targetPath = $mediaDirectory->getAbsolutePath($config->getBaseTmpMediaPath());
        
        if($this->_helper->getAspectratioflag() == 1) {
            $keepRatio = true;
        } else {
            $keepRatio = false;
        }
        
        if($this->_helper->getKeepframe() == 1) {
            $keepFrame = true;
        } else {
            $keepFrame = false;
        }
        
        $this->resizeFile($targetPath . $fileName, $keepRatio, $keepFrame, $fileName);

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }


    public function resizeFile($source, $keepRation = true, $keepFrame = true, $fileName)
    {
        if (!is_file($source) || !is_readable($source)) {
            return false;
        }
        $targetDir = $this->getThumbsPath($source);
        
        
        $width = $this->_helper->getThumbWidth();  
        $height = $this->_helper->getThumbHeight(); 
        $bgColor = $this->_helper->getBgcolor();

        $bgColorArray = explode(",", $bgColor);
        $imageObj = $this->_imageFactory->create($source);
        $imageObj->constrainOnly(TRUE);
        $imageObj->keepAspectRatio($keepRation);
        $imageObj->keepFrame($keepFrame);
        $imageObj->backgroundColor(array(intval($bgColorArray[0]),intval($bgColorArray[1]),intval($bgColorArray[2])));
        $imageObj->resize($width, $height);
        $dest = $targetDir . '/' . pathinfo($source, PATHINFO_BASENAME);
        $imageObj->save($dest);
       
        if (is_file($dest)) {
            return $dest;
        }
        return false;
    }


    /**
     * Return thumbnails directory path for file/current directory
     *
     * @param string $filePath Path to the file
     * @return string
     */
    public function getThumbsPath($filePath = false)
    {
       $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
       $config = $this->_objectManager->get('Smv\Ebaygallery\Model\Media\Config');
       $mediaRootDir = $mediaDirectory->getAbsolutePath($config->getBaseTmpMediaPath());
       $thumbnailDir = $mediaDirectory->getAbsolutePath($config->getBaseTmpMediaPath());
        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            $thumbnailDir .= dirname(substr($filePath, strlen($mediaRootDir)));
        }
        $thumbnailDir .= '/'."thumb";
        return $thumbnailDir;
    }

}
