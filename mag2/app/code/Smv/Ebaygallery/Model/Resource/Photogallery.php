<?php
namespace Smv\Ebaygallery\Model\Resource;




class Photogallery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**---Functions---*/
    /**
     * Store model
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Resource\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_objectManager = $objectManager;
    }
    

    
    public function _construct()
    {    
        $this->_init('photogallery', 'photogallery_id');
    }
	
	 protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
    	
        $select = $this->getConnection()->select()
            ->from($this->getTable('photogallery_store'))
            ->where('photogallery_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }
	
	//Get Category Ids
		$select = $this->getConnection()->select()
            ->from($this->getTable('photogallery_products'))
            ->where('photogallery_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $productsArray = array();
            foreach ($data as $row) {
                $productsArray[] = $row['product_id'];
            }
            $object->setData('product_id', $productsArray);
        }

        $category_ids = $object->getData("category_ids");
                    if($category_ids != "") {
                        $object->setData("photogallery_categories", $category_ids);
                    }

        return parent::_afterLoad($object);
        
    }
	
	/**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
		
        $condition = $this->getConnection()->quoteInto('photogallery_id = ?', $object->getId());
	//Get All Selected Categories
		$links = $object['links'];
        if (isset($links['related'])) {
            $productIds = $this->_objectManager->get('Magento\Backend\Helper\Js')->decodeGridSerializedInput($links['related']);
			$this->getConnection()->delete($this->getTable('photogallery_products'), $condition);	
			
			
					
			 foreach ($productIds as $_productId) {	 
			 
				$productsArray = array();
				$productsArray['photogallery_id'] = $object->getId();
				$productsArray['product_id'] = $_productId;
				$this->getConnection()->insert($this->getTable('photogallery_products'), $productsArray);
			}
		}
		
		if(isset($_POST['stores'])) {
			 $this->getConnection()->delete($this->getTable('photogallery_store'), $condition);
			foreach ((array)$_POST['stores'] as $store) {
				$storeArray = array();
				$storeArray['photogallery_id'] = $object->getId();
				$storeArray['store_id'] = $store;
				$this->getConnection()->insert($this->getTable('photogallery_store'), $storeArray);
			}
		}
    
        return parent::_afterSave($object);
        
    }


}