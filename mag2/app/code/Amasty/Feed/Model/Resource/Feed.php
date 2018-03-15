<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */

namespace Amasty\Feed\Model\Resource;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Feed extends AbstractDb
{
    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_feed_entity', 'entity_id');
    }
}