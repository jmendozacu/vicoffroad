<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Smv\Pdshipping\Model\Config\Source;
use Smv\Pdshipping\Block\BaseBlock;

class GetAllCountries implements \Magento\Framework\Option\ArrayInterface
{
    protected $_BaseBlock;
    public function __construct(
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        BaseBlock $baseBlock
    )
    {
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_BaseBlock = $baseBlock;

    }
    public function toOptionArray()
    {

        $options =$this->_BaseBlock->getAllCountry();
        $arroption=[];
        for($i=0;$i<count($options);$i++)
        {
            $arroption[] = ['value' => $options[$i]["value"], 'label' => __($options[$i]["label"])];
        }
        return $arroption;
    }
}