<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Model\Config\Source;

class SortFields implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'name', 'label' => __('Name')],
            ['value' => 'price', 'label' => __('Price')],
            ['value' => 'created_at', 'label' => __('Created date')],
            ['value' => 'updated_at', 'label' => __('Updated date')],
            ['value' => 'position', 'label' => __('Position')],
        ];
    }
}
