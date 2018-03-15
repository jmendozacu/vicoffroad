<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty Ltd. ( http://www.amasty.com/ )
 * @package Amasty_Oaction
 */
namespace Amasty\Oaction\Model\Source;

class Carriers implements \Magento\Framework\Option\ArrayInterface
{
    protected $_shippingConfig;

    public function __construct(
        \Magento\Shipping\Model\Config $shippingConfig
    ) {
        $this->_shippingConfig = $shippingConfig;
    }

    public function toOptionArray()
    {
        $options = array();
        $options[] = array(
            'value' => 'custom',
            'label' => __('Custom')
        );

        foreach ($this->_shippingConfig->getAllCarriers() as $k => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $options[] = array(
                    'value' => $k,
                    'label' => $carrier->getConfigData('title'),
                );
            }
        }

        return $options;
    }
}