<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ubertheme\UbContentSlider\Api\Data;

/**
 * UbContentSlider slide item interface.
 * @api
 */
interface ItemInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ITEM_ID                  = 'item_id';
    const SLIDE_ID                 = 'slide_id';
    const TITLE                    = 'title';
    const LINK                     = 'link';
    const TARGET                   = 'target';
    const CONTENT_TYPE             = 'content_type';
    const VIDEO_ID                 = 'video_id';
    const IMAGE                    = 'image';
    const DESCRIPTION              = 'description';
    const CREATION_TIME            = 'creation_time';
    const UPDATE_TIME              = 'update_time';
    const IS_ACTIVE                = 'is_active';
    const SORT_ORDER               = 'sort_order';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get slide id
     *
     * @return string
     */
    public function getSlideId();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();
    
    /**
     * Get link
     *
     * @return string|null
     */
    public function getLink();
    
    /**
     * Get target
     *
     * @return string|null
     */
    public function getTarget();
    
    /**
     * Get content_type
     *
     * @return string|null
     */
    public function getContentType();
    
    /**
     * Get video id
     *
     * @return string|null
     */
    public function getVideoId();
    
    /**
     * Get image
     *
     * @return string|null
     */
    public function getImage();
    
    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Get sort order
     *
     * @return string|null
     */
    public function getSortOrder();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setId($id);

    /**
     * Set slide id
     *
     * @param string $slideId
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setSlideId($slideId);

    /**
     * Set title
     *
     * @param string $title
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setTitle($title);
    
    /**
     * Set link
     *
     * @param string $link
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setLink($link);
    
    /**
     * Set target
     *
     * @param string $target
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setTarget($target);
    
    /**
     * Set content type
     *
     * @param string $contentType
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setContentType($contentType);
    
    /**
     * Set video id
     *
     * @param string $videoId
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setVideoId($videoId);
    
    /**
     * Set image
     *
     * @param string $image
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setImage($image);

    /**
     * Set description
     *
     * @param string $description
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setDescription($description);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set sort order
     *
     * @param string $sortOrder
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Ubertheme\UbContentSlider\Api\Data\ItemInterface
     */
    public function setIsActive($isActive);
}
