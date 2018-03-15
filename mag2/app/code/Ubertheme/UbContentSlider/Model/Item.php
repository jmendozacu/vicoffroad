<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Model;

use Ubertheme\UbContentSlider\Api\Data\ItemInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * UbContentSlider Slide Item Model
 *
 * @method \Ubertheme\UbContentSlider\Model\ResourceModel\Item _getResource()
 * @method \Ubertheme\UbContentSlider\Model\ResourceModel\Item getResource()
 */
class Item extends \Magento\Framework\Model\AbstractModel implements ItemInterface, IdentityInterface
{
    /**#@+
     * Slide item's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * UbContentSlider slide item cache tag
     */
    const CACHE_TAG = 'ubcontentslider_item';

    /**
     * @var string
     */
    protected $_cacheTag = 'ubcontentslider_item';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'ubcontentslider_item';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ubertheme\UbContentSlider\Model\ResourceModel\Item');
    }

    /**
     * Prepare slide item's statuses.
     * Available event ubcontentslider_item_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ITEM_ID);
    }

    /**
     * Get slide id
     *
     * @return int
     */
    public function getSlideId()
    {
        return $this->getData(self::SLIDE_ID);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }
    
    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->getData(self::LINK);
    }
    
    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->getData(self::TARGET);
    }
    
    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->getData(self::CONTENT_TYPE);
    }
    
    /**
     * Get video id
     *
     * @return string
     */
    public function getVideoId()
    {
        return $this->getData(self::VIDEO_ID);
    }
    
    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get update time
     *
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Get sort order
     *
     * @return string
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ITEM_ID, $id);
    }

    /**
     * Set slide id
     *
     * @param string $slideId
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setSlideId($slideId)
    {
        return $this->setData(self::SLIDE_ID, $slideId);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }
    
    /**
     * Set link
     *
     * @param string $link
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setLink($link)
    {
        return $this->setData(self::LINK, $link);
    }
    
    /**
     * Set target
     *
     * @param string $target
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setTarget($target)
    {
        return $this->setData(self::TARGET, $target);
    }
    
    /**
     * Set content type
     *
     * @param string $contentType
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setContentType($contentType)
    {
        return $this->setData(self::CONTENT_TYPE, $contentType);
    }
    
    /**
     * Set video id
     *
     * @param string $videoId
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setVideoId($videoId)
    {
        return $this->setData(self::VIDEO_ID, $videoId);
    }
    
    /**
     * Set image
     *
     * @param string $image
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set sort order
     *
     * @param string $sortOrder
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
    
    public function getSliderOptions()
    {
        $rs = [];
        $sliderOptions = $this->_getResource()->getSliderOptions();
        if ($sliderOptions){
            foreach ($sliderOptions as $option){
                $rs[$option['slide_id']] = $option['title'];
            }
        }
        
        return $rs;
    }
    
    public function getLinkTargetOptions()
    {
        return [
            '_blank' => __('Blank'), 
            '_self' => __('Self'), 
            '_top' => __('Top'), 
            '_parent' => __('Parent'), 
        ];
    }
    
    public function getContentTypeOptions()
    {
        return [
            'image' => __('Image'), 
            'youtube_video' => __('Youtube Video'), 
            'vimeo_video' => __('Vimeo Video'), 
        ];
    }
}
