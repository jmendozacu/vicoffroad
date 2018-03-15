<?php
namespace Smv\Ebaygallery\Model;

class Photogallery extends \Magento\Framework\Model\AbstractModel
{
	
    const STATUS_ENABLED = 1;
    

    const STATUS_DISABLED = 2;

	protected $_objectManager;

    protected $_coreResource;

    /**---Functions---*/
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \Smv\Ebaygallery\Model\Resource\Photogallery $resource,
        \Smv\Ebaygallery\Model\Resource\Photogallery\Collection $resourceCollection
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreResource = $coreResource;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    public function _construct()
    {
        $this->_init('Smv\Ebaygallery\Model\Resource\Photogallery');
    }

    
    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getRelatedProducts($attachmentId)
    {
                    
        $photogalleryTable = $this->_coreResource
                                    ->getTableName('photogallery_products');
            
        $collection = $this->_objectManager->create('Smv\Ebaygallery\Model\Photogallery')
                        ->getCollection()
                        ->addFieldToFilter('main_table.photogallery_id',$attachmentId);
                      
                      
        $collection->getSelect()
            ->joinLeft(array('related' => $photogalleryTable),
                        'main_table.photogallery_id = related.photogallery_id'
                )
            ->order('main_table.photogallery_id');
                    return $collection->getData();
    }
	
	public function checkPhotogallery($id)
    {
        return $this->_getResource()->checkPhotogallery($id);
    }
	
	/*
     * Delete Photogallery Stores
     * @return Array
     */
	public function deletePhotogalleryStores($id){
		return $this->getResource()->deletePhotogalleryStores($id);
		
	}
	
	/*
     * Delete Photogallery Product Links
     * @return Array
     */
	public function deletePhotogalleryProductLinks($id){
		return $this->getResource()->deletePhotogalleryProductLinks($id);
		
	}
	
	/**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param   string $identifier
     * @param   int $storeId
     * @return  int
     */
    public function checkIdentifier($identifier)
    {
        return $this->_getResource()->checkIdentifier($identifier);
    }
    
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

}