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
interface SlideSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get slides list.
     *
     * @return \Ubertheme\UbContentSlider\Api\Data\SlideInterface[]
     */
    public function getItems();

    /**
     * Set slides list.
     *
     * @param \Ubertheme\UbContentSlider\Api\Data\SlideInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
