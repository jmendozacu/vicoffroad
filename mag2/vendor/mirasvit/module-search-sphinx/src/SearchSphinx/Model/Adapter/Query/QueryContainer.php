<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-sphinx
 * @version   1.0.49
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mirasvit\SearchSphinx\Model\Adapter\Query;

use Mirasvit\SearchSphinx\SphinxQL\SphinxQL;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;

class QueryContainer
{
    const DERIVED_QUERY_PREFIX = 'derived_';
    /**
     * @var array
     */
    private $queries = [];

    /**
     * @var MatchContainerFactory
     */
    private $matchContainerFactory;

    /**
     * @param MatchContainerFactory $matchContainerFactory
     */
    public function __construct(MatchContainerFactory $matchContainerFactory)
    {
        $this->matchContainerFactory = $matchContainerFactory;
    }

    /**
     * @param SphinxQL              $select
     * @param RequestQueryInterface $query
     * @param string                $conditionType
     * @return Select
     */
    public function addMatchQuery(
        SphinxQL $select,
        RequestQueryInterface $query,
        $conditionType
    ) {
        $container = $this->matchContainerFactory->create([
            'request'       => $query,
            'conditionType' => $conditionType,
        ]);
        $name = self::DERIVED_QUERY_PREFIX . count($this->queries);
        $this->queries[$name] = $container;

        return $select;
    }

    /**
     * @return MatchContainer[]
     */
    public function getMatchQueries()
    {
        return $this->queries;
    }
}
