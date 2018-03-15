<?php
namespace Smv\Ebaygallery\Model\Resource\Photogallery;




class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    protected $_previewFlag;
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
    }

    /**---Functions---*/
     
    public function _construct()
    {
        
        $this->_init('\Smv\Ebaygallery\Model\Photogallery', '\Smv\Ebaygallery\Model\Resource\Photogallery');
        $this->_map['fields']['photogallery_id'] = 'main_table.photogallery_id';
        $this->_map['fields']['store'] = 'store_table.store_id';

    }

	
    public function addPhotogalleryFilter($photogallery)
    {
        if (is_array($photogallery)) {
            $condition = $this->getConnection()->quoteInto('main_table.photogallery_id IN(?)', $photogallery);
        }
        else {
            $condition = $this->getConnection()->quoteInto('main_table.photogallery_id=?', $photogallery);
        }
        return $this->addFilter('photogallery_id', $condition, 'string');
    }
    
    /**
     * Retrieve Product Galleries
     *
     * @return array
     */
    public function getPgalleries($productId)
    {
	
	$this->setConnection($this->getResource()->getConnection());
	$this->getSelect()
		->from(array('p' => $this->getTable('photogallery_products')),'p.photogallery_id')
		->joinLeft(array('g' => $this->getTable('photogallery')),
		    'g.photogallery_id = p.photogallery_id','*')
		->where('p.product_id = ?', $productId)
		->where('g.show_in = 2 OR g.show_in = 3')
		->order('g.gorder', 'ASC')
        ;
		
	return $this;
    }
    
    
    
    /**
     * Retrieve Product Photogallery Images
     *
     * @return array
     */
    public function getPimages($photogalleryIds)
    {
	$this->setConnection($this->getResource()->getConnection());
	$this->getSelect()
		->from(array('g' => $this->getTable('photogallery_images')),'*')
		->where('g.photogallery_id IN (?)', $photogalleryIds)
		->order('g.img_order', 'ASC')
        ;
	return $this;
    }
    
    

	 public function addStoreFilter($store)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = array($store->getId());
        }

        $this->getSelect()->join(
            array('store_table' => $this->getTable('photogallery_store')),
            'main_table.photogallery_id = store_table.photogallery_id',
            array()
        )
        ->where('store_table.store_id in (?)', array(0, $store));

        return $this;
    }

    protected function performAfterLoad($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['store_table' => $this->getTable($tableName)])
                ->where('store_table.' . $columnName . ' IN (?)', $items);
            $result = $connection->fetchPairs($select);
            if ($result) {
                foreach ($this as $item) {
                    $entityId = $item->getData($columnName);
                    if (!isset($result[$entityId])) {
                        continue;
                    }
                    if ($result[$entityId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData($columnName)];
                        $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', [$result[$entityId]]);
                }
            }
        }
    }

    protected function _afterLoad()
    {
        $this->performAfterLoad('photogallery_store', 'photogallery_id');
        $this->_previewFlag = false;
        return parent::_afterLoad();
    }

}