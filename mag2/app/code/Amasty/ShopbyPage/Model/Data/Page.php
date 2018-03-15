<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\ShopbyPage\Model\Data;

/**
 * Class Page
 *
 * @author Artem Brunevski
 */

use Amasty\ShopbyPage\Api\Data\PageInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Page extends AbstractExtensibleObject implements PageInterface
{
    /**
     * @return int
     */
    public function getPageId()
    {
        return $this->_get(self::PAGE_ID);
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_get(self::URL);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->_get(self::META_TITLE);
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->_get(self::META_KEYWORDS);
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->_get(self::META_DESCRIPTION);
    }

    /**
     * @return string[][]
     */
    public function getConditions()
    {
        return $this->_get(self::CONDITIONS);
    }

    /**
     * @return string[]
     */
    public function getCategories()
    {
        return $this->_get(self::CATEGORIES);
    }

    /**
     * @return int[]
     */
    public function getStores()
    {
        return $this->_get(self::STORES);
    }

    /**
     * @return int
     */
    public function getTopBlockId()
    {
        return $this->_get(self::TOP_BLOCK_ID);
    }

    /**
     * @return int
     */
    public function getBottomBlockId()
    {
        return $this->_get(self::BOTTOM_BLOCK_ID);
    }

    /**
     * @param int
     * @return PageInterface
     */
    public function setPageId($pageId)
    {
        return $this->setData(self::PAGE_ID, $pageId);
    }

    /**
     * @param int
     * @return PageInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * @param string
     * @return PageInterface
     */
    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * @param string
     * @return PageInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @param string
     * @return PageInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @param string
     * @return PageInterface
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * @param string
     * @return PageInterface
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * @param string
     * @return PageInterface
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * @param string[][]
     * @return PageInterface
     */
    public function setConditions($conditions)
    {
        return $this->setData(self::CONDITIONS, $conditions);
    }

    /**
     * @param string[]
     * @return PageInterface
     */
    public function setCategories($categories)
    {
        return $this->setData(self::CATEGORIES, $categories);
    }

    /**
     * @param int[]
     * @return PageInterface
     */
    public function setStores($stores)
    {
        return $this->setData(self::STORES, $stores);
    }

    /**
     * @param int
     * @return PageInterface
     */
    public function setTopBlockId($topBlockId)
    {
        return $this->setData(self::TOP_BLOCK_ID, $topBlockId);
    }

    /**
     * @param int
     * @return PageInterface
     */
    public function setBottomBlockId($bottomBlockId)
    {
        return $this->setData(self::BOTTOM_BLOCK_ID, $bottomBlockId);
    }

    /**
     * @return mixed
     */
    public function getData($key)
    {
        return $this->_get($key);
    }
}