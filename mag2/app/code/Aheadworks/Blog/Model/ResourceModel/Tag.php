<?php
namespace Aheadworks\Blog\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;

/**
 * Tag resource model
 * @package Aheadworks\Blog\Model\ResourceModel
 */
class Tag extends AbstractResource
{
    protected function _construct()
    {
        $this->_init('aw_blog_tag', 'id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->attachPosts($object);
        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$this->validateUniqueName($object)) {
            throw new LocalizedException(__('Tag name already exist.'));
        }
        $object->setCount(count($object->getPosts()));
        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->updatePosts($object);
        return parent::_afterSave($object);
    }

    /**
     * Update tag to posts linkage table
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    private function updatePosts(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this->updateLinkageTable(
            $object->getPosts(),
            $this->getPosts($object),
            $this->getTable('aw_blog_post_tag'),
            $object->getId(),
            'tag_id',
            'post_id'
        );
    }

    /**
     * Attach posts data to model
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    private function attachPosts(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setPosts($this->getPosts($object));
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return array
     */
    private function getPosts(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('aw_blog_post_tag'), 'post_id')
            ->where('tag_id = :id');
        return $connection->fetchCol($select, ['id' => $object->getId()]);
    }

    /**
     * Validate that tag name is unique
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function validateUniqueName(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('name = :name');
        $bind = ['name' => $object->getName()];
        if ($object->getId()) {
            $select->where($this->getIdFieldName() . ' <> :id');
            $bind['id'] = $object->getId();
        }
        if (!$connection->fetchRow($select, $bind)) {
            return true;
        }
        return false;
    }

    /**
     * @return \Magento\Framework\Validator\DataObject|null
     */
    public function getValidationRulesBeforeSave()
    {
        $validator = new \Magento\Framework\Validator\DataObject();

        $nameNotEmpty = new \Zend_Validate_NotEmpty();
        $nameNotEmpty->setMessage(__('Empty tags are not allowed.'), \Zend_Validate_NotEmpty::IS_EMPTY);
        $validator->addRule($nameNotEmpty, 'name');

        return $validator;
    }
}
