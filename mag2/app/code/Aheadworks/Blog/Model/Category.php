<?php
namespace Aheadworks\Blog\Model;

/**
 * Category model
 *
 * @method string getName()
 * @method string getUrlKey()
 * @method int getStatus()
 * @method string getMetaDescription()
 *
 * @method Category setName(string $name)
 * @method Category setUrlKey(string $urlKey)
 * @method Category setStatus(int $status)
 * @method Category setSortOrder(int $sortOrder)
 * @method Category setMetaTitle(string $metaTitle)
 * @method Category setMetaDescription(string $metaDescription)
 * @method Category setStores(array $stores)
 *
 * @package Aheadworks\Blog\Model
 */
class Category extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'aw_blog_category';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aheadworks\Blog\Model\ResourceModel\Category');
    }

    /**
     * Retrieves category ID by URL-Key. Returns null, if not exists
     *
     * @param string $urlKey
     * @return int|null
     */
    public function getIdByUrlKey($urlKey)
    {
        return $this->getResource()->getIdByUrlKey($urlKey);
    }
}
