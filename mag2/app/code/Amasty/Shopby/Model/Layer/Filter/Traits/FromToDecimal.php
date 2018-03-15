<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Model\Layer\Filter\Traits;
/**
 * Copyright © 2016 Amasty. All rights reserved.
 */
trait FromToDecimal
{
    use FilterTrait;

    /**
     * set from and to values for decimal filter
     * @param $from
     * @param $to
     *
     * @return $this
     */
    protected function setFromTo($from, $to)
    {
        list($from, $to) = $this->prepareFromTo($from, $to);
        $this->setCurrentValue(['from'=>$from, 'to'=>$to]);
        return $this;
    }

    /**
     * @return null
     */
    public function getCurrentFrom()
    {
        return $this->getCurrentByKey('from');
    }

    /**
     * @return null
     */
    public function getCurrentTo()
    {
        return $this->getCurrentByKey('to');
    }

    /**
     * @param $key
     *
     * @return null
     */
    protected function getCurrentByKey($key)
    {
        $current = null;
        if($this->hasCurrentValue()) {
            $current = $this->currentValue[$key];
        }
        return $current;
    }

    /**
     * @param $from
     * @param $to
     *
     * @return array
     */
    protected function prepareFromTo($from, $to)
    {
        if($from > $to) {
            $toTmp = $to;
            $to = $from;
            $from = $toTmp;
        }

        return [$from, $to];
    }


}
