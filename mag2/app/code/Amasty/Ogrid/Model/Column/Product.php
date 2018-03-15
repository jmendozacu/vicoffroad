<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */

namespace Amasty\Ogrid\Model\Column;

use Magento\Framework\Data\Collection;

class Product extends \Amasty\Ogrid\Model\Column
{
    protected $_alias_prefix = 'amasty_ogrid_product_';

    public function addFieldToSelect($collection)
    {
        $collection->getSelect()->columns([
            $this->_alias_prefix . $this->_fieldKey => $this->_fieldKey
        ]);

        foreach($this->_columns as $column){
            $collection->getSelect()->columns([
                $this->_alias_prefix . $column => $column
            ]);
        }
    }

    public function addFieldToFilter($orderItemCollection, $value)
    {
        if (is_array($value) &&
            array_key_exists('from', $value) &&
            array_key_exists('to', $value)
        ){
            $orderItemCollection->addFieldToFilter('main_table.' . $this->_fieldKey, [
                'between' => $value
            ]);
        } else {
            $orderItemCollection->addFieldToFilter('main_table.' . $this->_fieldKey, [
                'like' => '%'. $value . '%'
            ]);
        }
    }
}