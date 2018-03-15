<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Api\Data;

/**
 * interface FromToFilterInterface.php
 *
 * @author Artem Brunevski
 */

interface FromToFilterInterface
{
    /**
     * @return string[]
     */
    public function getFromToConfig();
}