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

class Status extends \Amasty\Oaction\Model\Command
{
    /**
     * @var \Amasty\Oaction\Helper\Data
     */
    protected $helper;

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
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        Registry $registry,
        InvoiceSender $invoiceCommentSender

    ) {
        parent::__construct();
        $this->helper = $helper;
        $this->objectManager = $objectManager;
        $this->orderApi = $orderApi;
        $this->invoiceService = $invoiceService;
        $this->invoiceCommentSender = $invoiceCommentSender;
        $this->registry = $registry;
    }

    /**
     * Executes the command
     *
     * @param AbstractCollection $collection
     * @param int $notify
     * @return string success message if any
     */
    public function execute(AbstractCollection $collection, $status, $oaction)
    {
        $numAffectedOrders = 0;

        $comment = __('Status changed');

        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $orderCode = $order->getIncrementId();

            try {
                $state = $order->getState();
                $statuses = $this->objectManager->create('Magento\Sales\Model\Order\Status')
                    ->getCollection()
                    ->addStateFilter($state)
                    ->toOptionHash();
                if (!array_key_exists($status, $statuses) && $this->helper->getModuleConfig('status/check_state')) {
                    $err = __('Selected status does not correspond to the state of order.');
                    $this->_errors[] = __(
                        'Can not update order #%1: %2', $orderCode, $err);
                    continue;
                }

                $history = $order->addStatusHistoryComment($comment, $status);
                $history->setIsVisibleOnFront(false);
                $history->setIsCustomerNotified(false);
                $history->save();

                $order->save();

                ++$numAffectedOrders;
            }
            catch (\Exception $e) {
                $err = $e->getCustomMessage();
                $this->_errors[] = __('Can not update order #%1: %2', $orderCode, $err);
            }
            unset($order);
        }

        $success = ($numAffectedOrders)?
            $success = __('Total of %1 order(s) have been successfully updated.', $numAffectedOrders)
            : '';

        return $success;

    }

}
