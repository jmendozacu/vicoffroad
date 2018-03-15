<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Model\Config\Source;

class SelectSlider implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        $options = [];
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $om->get('Ubertheme\UbContentSlider\Model\Slide');
        $availableOptions = $model->getOptions();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
