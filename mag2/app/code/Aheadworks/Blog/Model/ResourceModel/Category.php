<?php
namespace Aheadworks\Blog\Model\ResourceModel;

/**
 * Category resource model
 * @package Aheadworks\Blog\Model\ResourceModel
 */
class Category extends AbstractResource
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_blog_cat', 'cat_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->attachStores($object);
        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->updateStores($object);
        return parent::_afterSave($object);
    }

    /**
     * @return \Magento\Framework\Validator\DataObject|null
     */
    public function getValidationRulesBeforeSave()
    {
        $validator = new \Magento\Framework\Validator\DataObject();

        $nameNotEmpty = new \Zend_Validate_NotEmpty();
        $nameNotEmpty->setMessage(__('Name is required.'), \Zend_Validate_NotEmpty::IS_EMPTY);
        $validator->addRule($nameNotEmpty, 'name');

        $this
            ->addUrlKeyValidateRules($validator)
            ->addStoresValidateRules($validator);

        return $validator;
    }
}
