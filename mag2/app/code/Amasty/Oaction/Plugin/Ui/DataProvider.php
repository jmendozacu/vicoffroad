<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_Oaction
 */
namespace Amasty\Oaction\Plugin\Ui;

class DataProvider
{
    /**
     * @var \Amasty\Oaction\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    public function __construct(
        \Amasty\Oaction\Helper\Data $helper,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_helper = $helper;
        $this->_carrierFactory = $carrierFactory;
        $this->objectManager = $objectManager;
    }

    /*
     * Create xml config with php for enable\disable it from admin panel
     * */
    public function aroundGetData(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $subject,
        \Closure $proceed
    )
    {
        $result = $proceed();
        if ( $subject->getName() == 'sales_order_grid_data_source' ) {
            if (array_key_exists('items', $result)) {
                foreach($result['items'] as $id => $item) {
                    $orderId = $item['entity_id'];
                    $order = $this->objectManager->create('Magento\Sales\Model\Order')->load($orderId);
                    /** @var \Magento\Sales\Model\Order $order */

                    $canShip = $order->canShip();
                    if ($canShip) {
                        $result['items'][$id]['track_exist'] = 1;
                        continue;
                    }

                    $collection = $this->objectManager->create('Magento\Sales\Model\Order\Shipment\Track')
                        ->getCollection()
                        ->addAttributeToSelect('track_number')
                        ->addAttributeToSelect('title')
                        ->setOrderFilter($orderId);

                    $numbers = array();
                    $carriers = array();
                    foreach ($collection as $track) {
                        $numbers[]  = $track->getData('track_number');
                        $carriers[] = $track->getTitle();
                    }
                    $result['items'][$id]['track_exist'] = $carriers? 1: 0;
                    $result['items'][$id]['current_tracking'] = implode(', ', $numbers);
                    $result['items'][$id]['current_carrier']  = implode(', ', $carriers);
                }
            }
        }

        return $result;
    }
}