<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */

namespace Amasty\Feed\Model\Resource\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Amasty\Feed\Model\Category', 'Amasty\Feed\Model\Resource\Category');
    }
}