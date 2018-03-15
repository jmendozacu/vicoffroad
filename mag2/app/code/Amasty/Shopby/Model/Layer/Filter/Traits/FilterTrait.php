<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Shopby\Model\Layer\Filter\Traits;


trait FilterTrait
{
    /**
     * @var current applied value
     */
    protected $currentValue;

    /**
     * @param set $currentValue
     */
    protected function setCurrentValue($currentValue)
    {
        $this->currentValue = $currentValue;
    }

    /**
     * @return bool is filter applied
     */
    protected function hasCurrentValue()
    {
        return !is_null($this->currentValue);
    }

}
