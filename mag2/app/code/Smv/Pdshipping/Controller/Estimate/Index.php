<?php
/**
 *
 * Copyright © 2015 Smvcommerce. All rights reserved.
 */
namespace Smv\Pdshipping\Controller\Estimate;

class Index extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPost();
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = null;
        $quote = $object_manager->get('Magento\Quote\Model\Quote');
        $objectFactory = $object_manager->get('\Magento\Framework\DataObject\Factory');
        $productId = $postData['product'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
        /*******/
        $regonID=null;
        $RegionName=null;
        if ($postData['state_province_option']) {
            $regonID=$postData['state_province_option'];
            $RegionName=$this->getRegonName($regonID);
        }
        /******/
        $output = '<ul>';
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCountryId($postData['country_id']);
        $shippingAddress->setPostcode($postData['postcode']);
        $shippingAddress->setRegionId($regonID);
        $shippingAddress->setCity($postData['state_province_city']);
        $shippingAddress->setRegion($RegionName);
        $shippingAddress->setCollectShippingRates(true);
        $shippingAddress = $quote->getShippingAddress();
        if ($postData['qty'] < 1) {
            $postData['qty'] = 1;
        }
        $request = $objectFactory->create(['qty' => $postData['qty']]);

        $quote->addProduct($product, $request);
        $quote->collectTotals();

        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        $price=0;
        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $price = $object_manager->get('Magento\Framework\Pricing\Helper\Data')->currency(number_format($rate['price'], 2), true, false);
                $output .= '<li><span>' . $rate['carrier_title'] . '</span> ' . $price . '</li>';

            }
        }
        $output .= '</ul>';
        if (count($shippingRates)) {
            $this->getResponse()->setBody($output);
        } else {
            $output='<span style="display: block;color: blue;">Please contact us</span>';
            $this->getResponse()->setBody($output);
        }

    }
    public function getRegonName($id){
        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection = $this->_resources->getConnection();

        $select = $connection->select()
            ->from(
                ['o' => $this->_resources->getTableName('directory_country_region')]
            )->where('o.region_id=?', $id);

        $data = $connection->fetchAll($select);
        return $data;
    }
}
