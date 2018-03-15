<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Model\Config\Source;

class Owl1Transitions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'fade', 'label' => __('Fade')],
            ['value' => 'backSlide', 'label' => __('Back Slide')],
            ['value' => 'goDown', 'label' => __('Go Down')],
            ['value' => 'fadeUp', 'label' => __('Fade Up')],
        ];
    }
}
