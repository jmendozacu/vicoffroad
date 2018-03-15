<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ubertheme\UbContentSlider\Model\ResourceModel;

use Ubertheme\UbContentSlider\Helper\Image as ImageHelper;

/**
 * UbContentSlider slide item mysql resource
 */
class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ubcontentslider_slide_item', 'item_id');
    }

    /**
     * Process slide item data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        //delete related image uploaded
        $image = $object->getImage();
        if ($image) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $mediaDirectory = $om->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $imagePath = $mediaDirectory->getAbsolutePath($image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $condition = ['item_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getMainTable(), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process slide item data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        //validate video id
        if (in_array($object->getData('content_type'), ['youtube_video', 'vimeo_video'])) {
            if (!ImageHelper::isValidVideoId($object->getData('content_type'), $object->getData('video_id'))) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please re-check, the Video ID and Content Type you selected are not matched.')
                );
            }
        }

        return parent::_beforeSave($object);
    }

    /**
     * After save a slide item function
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return parent::_afterSave($object);
    }

    /**
     * Load an object
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Ubertheme\UbContentSlider\Model\Item $object
     * @return \Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        return $select;
    }

    /**
     * Retrieves slide item title from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getSlideItemTitleById($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable(), 'title')->where('item_id = :item_id');
        $binds = ['item_id' => (int)$id];
        return $connection->fetchOne($select, $binds);
    }
    
    /**
     * Retrieves avaiable sliders.
     * @return array
     */
    public function getSliderOptions()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getTable('ubcontentslider_slide'), ['slide_id', 'title']);
        return $connection->fetchAll($select);
    }
}
