<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for ubcs slide search results.
 * @api
 */
interface ItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get slide items list.
     *
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface[]
     */
    public function getItems();

    /**
     * Set slide items list.
     *
     * @param \Ubertheme\UbContentSlider\Api\Data\ItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
