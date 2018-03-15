<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Model\Item\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Slider Options
 */
class SliderOptions implements OptionSourceInterface
{
    /**
     * @var \Ubertheme\UbContentSlider\Model\Item
     */
    protected $item;

    /**
     * Constructor
     *
     * @param \Ubertheme\UbContentSlider\Model\Item $item
     */
    public function __construct(\Ubertheme\UbContentSlider\Model\Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->item->getSliderOptions();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
