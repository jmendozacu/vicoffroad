<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Model\Config\Source;

class ProductTypes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'latest_products', 'label' => __('Latest Products')],
            ['value' => 'new_products', 'label' => __('New Products (From...To.. Date)')],
            ['value' => 'hot_products', 'label' => __('Hot Products')],
            ['value' => 'random_products', 'label' => __('Show Random Products')]
        ];
    }
}
