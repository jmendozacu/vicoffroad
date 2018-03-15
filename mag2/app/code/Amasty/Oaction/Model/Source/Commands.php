<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_Oaction
 */
namespace Amasty\Oaction\Model\Source;

class Commands implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Amasty\Oaction\Helper\Data
     */
    protected $_helper;

    public function __construct(
        \Amasty\Oaction\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function toOptionArray()
    {
        $options = array();

        $types = array(
            ''                  => '',
            'invoice'           => 'Invoice',
           // 'invoicecapture'    => 'Invoice > Capture',
            'invoiceship'       => 'Invoice > Ship',
          //  'invoicecaptureship'=> 'Invoice > Capture > Ship',
          //  'captureship'       => 'Capture > Ship',
          //  'capture'           => 'Capture',
            'ship'              => 'Ship',
            'status'            => 'Change Status'
        );
        foreach ($types as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label

            );
        }
        return $options;
    }
}