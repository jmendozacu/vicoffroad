<?php
namespace Smv\Ebaygallery\Controller\Adminhtml;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Class AbstractMassDelete
 */
class AbstractMassDelete extends \Magento\Backend\App\Action
{

    /**
     * Redirect url
     */
    const REDIRECT_URL = '*/*/';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Magento\Framework\Model\Resource\Db\Collection\AbstractCollection';

    /**
     * Model
     *
     * @var string
     */
    protected $model = 'Magento\Framework\Model\AbstractModel';


    protected $cat = false;

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');

        try {
            if (isset($excluded)) {
                if ($excluded!='false') {
                    echo "<pre>";
                    print_r($excluded);
                    exit;
                    $this->excludedDelete($excluded);
                } else {
                    $this->deleteAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedDelete($selected);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(static::REDIRECT_URL);
    }

    /**
     * Delete all
     *
     * @return void
     * @throws \Exception
     */
    protected function deleteAll()
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $this->delete($collection);
    }

    /**
     * Delete all but the not selected
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedDelete(array $excluded)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->delete($collection);
    }

    /**
     * Delete selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedDelete(array $selected)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->delete($collection);
    }

    /**
     * Delete collection items
     *
     * @param AbstractCollection $collection
     * @return int
     */
    protected function delete(AbstractCollection $collection)
    {
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            /** @var \Magento\Framework\Model\AbstractModel $model */
                $model = $this->_objectManager->get($this->model);
                $this->deleteImages($id);
                $model->load($id);
                $model->delete();
                
                ++$count;
            
            
        }
        $this->setSuccessMessage($count);
        return $count;
    }



    protected function deleteImages($id)
    {       

        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
        $config = $this->_objectManager->get('Smv\Ebaygallery\Model\Media\Config');
        $mediaRootDir = $mediaDirectory->getAbsolutePath($config->getBaseTmpMediaPath());
        $object = $this->_objectManager->create('Smv\Ebaygallery\Model\ImgFactory');
        $coll = $object->create()->getCollection()->addFieldToFilter('photogallery_id',$id);

        foreach ($coll as $col) {
            $file_name = $col->getImgName();
            $imgPath=  $this->splitImageValue($file_name,"path");
            $imgName=  $this->splitImageValue($file_name,"name");
            $file_path = $mediaRootDir . $file_name;
            $thumb_path = $mediaRootDir .$imgPath. DIRECTORY_SEPARATOR.'thumb'.DIRECTORY_SEPARATOR.$imgName;
            if ($file_path) {       
//                unlink($file_path); 
//                unlink($thumb_path);
            }   
        }
    }


    /**
     * Set error messages
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
    }

    public function splitImageValue($imageValue,$attr="name"){
        $imArray=explode("/",$imageValue);

        $name=$imArray[count($imArray)-1];
        $path=implode("/",array_diff($imArray,array($name)));
        if($attr=="path"){
            return $path;
        }
        else
            return $name;

    }

    protected function _isAllowed()
    {
        return true;
    }
}
