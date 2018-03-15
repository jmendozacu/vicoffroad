<?php
namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Model\Source\Post\Status;

/**
 * Post model
 *
 * @method bool hasStatus()
 * @method string getStatus()
 * @method string getTitle()
 * @method string getUrlKey()
 * @method string getPublishDate()
 * @method string getShortContent()
 * @method string getContent()
 * @method string getMetaDescription()
 * @method int getIsAllowComments()
 * @method array getTags()
 * @method array getCategories()
 * @method array getStores()
 *
 * @method Post setTitle(string $title)
 * @method Post setUrlKey(string $urlKey)
 * @method Post setStatus(string $status)
 * @method Post setShortContent(string $shortContent)
 * @method Post setContent(string $content)
 * @method Post setIsAllowComments(int $isAllowComments)
 * @method Post setMetaTitle(string $metaTitle)
 * @method Post setMetaDescription(string $metaDescription)
 * @method Post setStores(array $stores)
 * @method Post setCategories(array $categories)
 * @method Post setTags(array $tags)
 * @method Post setPublishDate(string $date)
 *
 * @package Aheadworks\Blog\Model
 */
class Post extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        if (!$this->hasStatus()) {
            $this->setStatus(Status::DRAFT);
        }
        $this->_init('Aheadworks\Blog\Model\ResourceModel\Post');
    }

    /**
     * Retrieves post ID by URL-Key. Returns null, if not exists
     *
     * @param string $urlKey
     * @return int|null
     */
    public function getIdByUrlKey($urlKey)
    {
        return $this->getResource()->getIdByUrlKey($urlKey);
    }

    public function getVirtualStatus()
    {
        $virtualStatus = null;
        if ($this->getStatus() == Status::DRAFT) {
            $virtualStatus = $this->getStatus();
        } elseif ($this->getStatus() == Status::PUBLICATION) {
            $now = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time());
            $virtualStatus = $this->getPublishDate() > $now ? //todo convert strtotime() before compare for more security
                Status::PUBLICATION_SCHEDULED :
                Status::PUBLICATION_PUBLISHED;
        }
        return $virtualStatus;
    }

    /**
     * @return array
     */
    public function getTagsAsOptionArray() {
        $tags = is_array($this->getTags()) ? $this->getTags() : [];
        $optionArray = [];
        foreach ($tags as $tagName) {
            $optionArray[] = ['value' => $tagName, 'label' => $tagName];
        }
        return $optionArray;
    }
}
