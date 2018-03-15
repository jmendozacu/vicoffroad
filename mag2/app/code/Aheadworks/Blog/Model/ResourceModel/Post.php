<?php
namespace Aheadworks\Blog\Model\ResourceModel;

use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\Exception\LocalizedException;

/**
 * Post resource model
 * @package Aheadworks\Blog\Model\ResourceModel
 */
class Post extends AbstractResource
{
    /**
     * @var \Aheadworks\Blog\Model\TagFactory
     */
    private $tagFactory;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Aheadworks\Blog\Model\TagFactory $tagFactory
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Aheadworks\Blog\Model\TagFactory $tagFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->tagFactory = $tagFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_blog_post', 'post_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->attachStores($object);
        $this->attachCategories($object);
        $this->attachTags($object);
        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->_updatePostStatus($object);
        $this->_validateScheduledDate($object);
        $now = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time());
        if ($object->getStatus() == Status::PUBLICATION && !$object->getPublishDate()) {
            $object->setPublishDate($now);
        }
        if (!$object->getId()) {
            $object->setCreatedAt($now);
        }
        $object->setUpdatedAt($now);
        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->updateStores($object);
        $this->updateCategories($object);
        $this->updateTags($object);
        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->removePostFromTags($object, $object->getTags());
        return parent::_afterDelete($object);
    }

    /**
     * Update post to categories linkage table
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    private function updateCategories(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this->updateLinkageTable(
            $object->getCategories(),
            $this->getCategories($object),
            $this->getTable('aw_blog_post_cat'),
            $object->getId(),
            $this->getIdFieldName(),
            'cat_id'
        );
    }

    /**
     * Attach categories data to model
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    private function attachCategories(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setCategories($this->getCategories($object));
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return array
     */
    private function getCategories(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('aw_blog_post_cat'), 'cat_id')
            ->where($this->getIdFieldName() . ' = :id');
        return $connection->fetchCol($select, ['id' => $object->getId()]);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    private function updateTags(\Magento\Framework\Model\AbstractModel $object)
    {
        $origTags = $this->getTags($object);
        $tags = $object->getTags();
        if (!is_array($tags)) {
            $tags = [];
        }
        array_walk($tags, [$this, 'prepareTagsData']);

        $newCandidates = array_diff($tags, $origTags);
        $removeCandidates = array_diff($origTags, $tags);

        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('aw_blog_tag'), 'LOWER(name)')
            ->where('name IN (?)', $newCandidates);
        $existingTags = $connection->fetchCol($select);

        foreach ($newCandidates as $candidate) {
            /** @var \Aheadworks\Blog\Model\Tag $tagModel */
            $tagModel = $this->tagFactory->create();
            if (!in_array(strtolower($candidate), $existingTags)) {
                $tagModel
                    ->setData(['name' => $candidate])
                    ->addPost($object->getId())
                    ->save();
            } else {
                $tagModel->loadByName($candidate)
                    ->addPost($object->getId())
                    ->save();
            }
        }
        $this->removePostFromTags($object, $removeCandidates);

        return $this;
    }

    /**
     * Prepares tag data before saving
     *
     * @param string $tag
     */
    private function prepareTagsData(&$tag)
    {
        $tag = trim($tag);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param array $tags
     */
    private function removePostFromTags(\Magento\Framework\Model\AbstractModel $object, $tags)
    {
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                /** @var \Aheadworks\Blog\Model\Tag $tagModel */
                $tagModel = $this->tagFactory->create();
                $tagModel->loadByName($tag)
                    ->removePost($object->getId())
                    ->setHasDataChanges(true);
                $tagModel->save();
            }
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    private function attachTags(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setTags($this->getTags($object));
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return array
     */
    private function getTags(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['tag' => $this->getTable('aw_blog_tag')], ['id','name'])
            ->joinLeft(
                ['tag_post' => $this->getTable('aw_blog_post_tag')],
                'tag.id = tag_post.tag_id',
                []
            )
            ->where('tag_post.post_id = ?', $object->getId());
        return $connection->fetchPairs($select);
    }

    /**
     * @return \Magento\Framework\Validator\DataObject|null
     */
    public function getValidationRulesBeforeSave()
    {
        $validator = new \Magento\Framework\Validator\DataObject();

        $titleNotEmpty = new \Zend_Validate_NotEmpty();
        $titleNotEmpty->setMessage(__('Title is required.'), \Zend_Validate_NotEmpty::IS_EMPTY);
        $validator->addRule($titleNotEmpty, 'title');

        $contentNotEmpty = new \Zend_Validate_NotEmpty();
        $contentNotEmpty->setMessage(__('Content is required.'), \Zend_Validate_NotEmpty::IS_EMPTY);
        $validator->addRule($contentNotEmpty, 'content');

        $this
            ->addUrlKeyValidateRules($validator)
            ->addStoresValidateRules($validator);

        return $validator;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    private function _updatePostStatus(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getStatus() || $object->getData('save_as_draft')) {
            $object->setStatus(Status::DRAFT);
        } else {
            $object->setStatus(Status::PUBLICATION);
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    private function _validateScheduledDate(\Magento\Framework\Model\AbstractModel $object)
    {
        $now = time();
        if ($object->hasData('is_scheduled') && strtotime($object->getPublishDate()) <= $now) {
            throw new LocalizedException(__("Publish date must be in future for scheduled posts"));
        }
        return $this;
    }
}
