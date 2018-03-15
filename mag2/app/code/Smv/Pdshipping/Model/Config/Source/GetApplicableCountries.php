<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Smv\Pdshipping\Model\Config\Source;

class GetApplicableCountries implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('All Allowed Countries')],
            ['value' => '1', 'label' => __('Specific Countries')]
        ];
    }
}