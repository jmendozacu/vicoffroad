<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */

namespace Amasty\Ogrid\Model\Column\Product;

use Magento\Framework\Data\Collection;

class Option extends \Amasty\Ogrid\Model\Column\Product
{
    public function getOrderOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

    public function modifyItem(&$item, $config = []){
        parent::modifyItem($item, $config);

        if (array_key_exists('amasty_ogrid_product_product_options', $item)){
            $options = $this->_getOrderOptions(unserialize($item['amasty_ogrid_product_product_options']));

            if (is_array($options)){
                foreach($options as $idx => $vals){
                    $options[$idx] = implode(": ", $vals);
                }
            }

            $item['amasty_ogrid_product_product_options'] = implode(", ", $options);
        }
    }

    protected function _getOrderOptions($options)
    {
        $result = [];

        if (isset($options['options'])) {
            $result = array_merge($result, $options['options']);
        }
        if (isset($options['additional_options'])) {
            $result = array_merge($result, $options['additional_options']);
        }
        if (!empty($options['attributes_info'])) {
            $result = array_merge($options['attributes_info'], $result);
        }

        return $result;
    }
}