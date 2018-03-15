<?php
/**
 *
 * Copyright © 2015 Smvcommerce. All rights reserved.
 */
namespace Smv\Pdshipping\Controller\Estimate;

class GetState extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
        $id_country = $this->getRequest()->getPost("country_id");
        $data=$this->GetStateCountry($id_country);
        echo(json_encode($data));
        die();
        
    }
    public function GetStateCountry($country_id)
    {

        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection = $this->_resources->getConnection();

        $select = $connection->select()
            ->from(
                ['o' => $this->_resources->getTableName('directory_country_region')]
            )->where('o.country_id=?', $country_id);

        $data = $connection->fetchAll($select);
        return $data;
    }
}
