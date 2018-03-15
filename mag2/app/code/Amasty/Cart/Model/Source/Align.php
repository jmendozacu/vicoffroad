<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Cart
 */
namespace Amasty\Cart\Model\Source;

class Align implements \Magento\Framework\Option\ArrayInterface
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
                'label' =>__('Center')
        );
        $options[] = array(
                'value' => '1',
                'label' =>__('Top')
        );
        $options[] = array(
                'value' => '2',
                'label' =>__('Top Left')
        );
        $options[] = array(
                'value' => '3',
                'label' =>__('Top Right')
        ); 
        $options[] = array(
                'value' => '4',
                'label' =>__('Left')
        ); 
        
        $options[] = array(
                'value' => '5',
                'label' =>__('Right')
        );
        return $options;
    }
}