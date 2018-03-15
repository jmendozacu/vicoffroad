<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Cart
 */
namespace Amasty\Cart\Model\Source;

class Option implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array(
                'value' => '0',
                'label' => __('Only Required Options')
        );
        $options[] = array(
                'value' => '1',
                'label' => __('All Custom Options')
        );
        return $options;
    }
}