<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */

namespace Amasty\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Compress implements ArrayInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach($arr as $value => $label){
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options =  [
            \Amasty\Feed\Model\Feed::COMPRESS_NONE => __('None'),
            \Amasty\Feed\Model\Feed::COMPRESS_ZIP => __('Zip'),
            \Amasty\Feed\Model\Feed::COMPRESS_GZ => __('Gz'),
            \Amasty\Feed\Model\Feed::COMPRESS_BZ => __('Bz')
        ];

        return $options;
    }
}