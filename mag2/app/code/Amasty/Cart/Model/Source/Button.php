<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Cart
 */
namespace Amasty\Cart\Model\Source;

class Button implements \Magento\Framework\Option\ArrayInterface
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
                'label' => __('Stay on Product View Page')
        );
        $options[] = array(
                'value' => '1',
                'label' => __('Go to Category Page')
        );
        return $options;
    }
}