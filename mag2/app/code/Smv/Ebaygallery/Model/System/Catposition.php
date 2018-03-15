<?php

namespace Smv\Ebaygallery\Model\System;




class Catposition extends \Magento\Framework\ObjectManager\ObjectManager {


    /**---Functions---*/
    public function toOptionArray() {
		return array(
            array(
                'label' => __('Top'),
                'value' => 'top'
            ),
            array(
                'label' => __('Bottom'),
                'value' => 'bottom'
            )
        );
	}


    public function __construct(
                    \Magento\Framework\ObjectManagerInterface $objectManager,
                 \Magento\Framework\ObjectManager\FactoryInterface $factory,
                 \Magento\Framework\ObjectManager\ConfigInterface $config
            )
         {
                parent::__construct($factory, $config);
                $this->_objectManager = $objectManager;
            

    }


}
