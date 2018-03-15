<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty Ltd. ( http://www.amasty.com/ )
 * @package Amasty_Oaction
 */
namespace Amasty\Oaction\Model\Command;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;

class Ship extends \Amasty\Oaction\Model\Command
{
    /**
     * @var \Amasty\Oaction\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected $_pdf = null;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Registry
     */
    protected $registry;



    public function __construct(
        \Amasty\Oaction\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Api\OrderManagementInterface $orderApi,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Amasty\Oaction\Model\Source\Carriers $carrier,
        ShipmentSender $shipmentSender,
        Registry $registry,
        InvoiceSender $invoiceCommentSender

    ) {
        parent::__construct();
        $this->helper = $helper;
        $this->objectManager = $objectManager;
        $this->orderApi = $orderApi;
        $this->shipmentSender = $shipmentSender;
        $this->shipmentLoader = $shipmentLoader;
        $this->registry = $registry;
        $this->carrier = $carrier;
    }

    /**
     * Executes the command
     *
     * @param AbstractCollection $collection
     * @param int $notify
     * @return string success message if any
     */
    public function execute(AbstractCollection $collection, $notifyCustomer, $oaction)
    {
        $numAffectedOrders = 0;

        $comment = __('Shipment created');

        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $orderCode = $order->getIncrementId();
            $orderId = $order->getId();

            try {
                $this->shipmentLoader->setOrderId($orderId);
                $this->shipmentLoader->setShipmentId(null);

                $this->shipmentLoader->setShipment(['comment_text' => $comment]);

                $traking = $this->_getTraking($oaction, $orderId);

                $this->shipmentLoader->setTracking($traking);
                $shipment = $this->shipmentLoader->load();
                if (!$shipment) {
                    throw new \Exception(__('We can\'t save the shipment right now.'));
                }

                $shipment->addComment(
                    $comment,
                    $notifyCustomer
                );

                $shipment->setCustomerNote($comment);
                $shipment->setCustomerNoteNotify($notifyCustomer);

                $shipment->register();

                $shipment->getOrder()->setCustomerNoteNotify($notifyCustomer);

                $this->_saveShipment($shipment);

                if ($notifyCustomer) {
                    $this->shipmentSender->send($shipment);
                }
                ++$numAffectedOrders;
            }
            catch (\Exception $e) {
                $err = $e->getMessage();
                $this->_errors[] = __('Can not ship order #%1: %2', $orderCode, $err);
            }
            $order = null;
            unset($order);
            $this->registry->unregister('current_shipment');
        }
        $success = '';
        if ($numAffectedOrders) {
            $success = __('Total of %1 order(s) have been successfully shipped.',
                $numAffectedOrders);
        }

        return $success;

    }

    protected function _getTraking($oaction, $orderId) {
        $result = null;
        if (is_array($oaction) && array_key_exists($orderId, $oaction)) {
            $carrier_code = $oaction[$orderId]['amasty-carrier'];
            $title  = (array_key_exists('amasty-comment',  $oaction[$orderId]) && $oaction[$orderId]['amasty-comment'] != '')? $oaction[$orderId]['amasty-comment']: $this->_getTitleByCode($oaction[$orderId]['amasty-carrier'], null);
            $number = (array_key_exists('amasty-tracking', $oaction[$orderId]))? $oaction[$orderId]['amasty-tracking']: '';
            $result[] = [
                'carrier_code'  => $carrier_code,
                'title'         => $title,
                'number'        => $number
            ];
        }

        return $result;
    }


    private function _getTitleByCode($code, $storeId)
    {
        if ($code == 'custom') {
            $title = $this->helper->getModuleConfig('ship/title', $storeId);
        } else {
            $carriers = $this->carrier->toOptionArray($storeId);
            foreach ($carriers as $carrier) {
                if ($code == $carrier['value']) {
                    $title = $carrier['label'];
                    break;
                }
            }
        }
        if (!$title)
            $title = $code;

        return $title;
    }

    /**
     * Save shipment and order in one transaction
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return $this
     */
    protected function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transaction = $this->objectManager->create(
            'Magento\Framework\DB\Transaction'
        );
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        return $this;
    }
}
