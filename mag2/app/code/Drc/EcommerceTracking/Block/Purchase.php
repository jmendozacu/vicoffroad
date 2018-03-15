<?php
namespace Drc\EcommerceTracking\Block;

class Purchase extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Drc\EcommerceTracking\Helper\Data
     */
    public $helper;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    public $checkoutSession;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Drc\EcommerceTracking\Helper\FBData $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Drc\EcommerceTracking\Helper\FBData $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->helper          = $helper;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }
    
    /**
     * Returns data needed for purchase tracking.
     *
     * @return array|null
     */
    public function getOrderData()
    {
        $order   = $this->checkoutSession->getLastRealOrder();
        $orderId = $order->getIncrementId();
        
        if ($orderId) {
            $items = [];
    
            foreach ($order->getAllVisibleItems() as $item) {
                $items[] = [
                    'name' => $item->getName(), 'sku' => $item->getSku()
                ];
            }
    
            $data = [];
    
            if (count($items) === 1) {
                $data['content_name'] = $this->helper
                    ->escapeSingleQuotes($items[0]['name']);
            }
    
            $ids = '';
            foreach ($items as $i) {
                $ids .= "'" . $this->helper
                    ->escapeSingleQuotes($i['sku']) . "', ";
            }
    
            $data['content_ids']  = trim($ids, ", ");
            $data['content_type'] = 'product';
            $data['value']        = number_format(
                $order->getGrandTotal(),
                2,
                '.',
                ''
            );
            $data['currency']     = $order->getOrderCurrencyCode();
    
            return $data;
        } else {
            return null;
        }
    }
}
