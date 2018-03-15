<?php
namespace Aheadworks\Blog\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;

/**
 * Abstract resource model of all blog entities
 * @package Aheadworks\Blog\Model\ResourceModel
 */
abstract class AbstractResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Updates store linkage table
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function updateStores(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this->updateLinkageTable(
            $object->getStores(),
            $this->getStores($object),
            $this->getStoreLinkageTable(),
            $object->getId(),
            $this->getIdFieldName(),
            'store_id'
        );
    }

    /**
     * Updates linkage table
     *
     * @param array $data
     * @param array $origData
     * @param string $table
     * @param int $id
     * @param string $idFieldName
     * @param string $linkIdFieldName
     * @return $this
     */
    protected function updateLinkageTable(
        $data,
        $origData,
        $table,
        $id,
        $idFieldName,
        $linkIdFieldName
    ) {
        $toInsert = array_diff($data, $origData);
        $toDelete = array_diff($origData, $data);

        $connection = $this->getConnection();
        foreach ($toInsert as $linkageId) {
            $connection->insert(
                $table,
                [$idFieldName => $id, $linkIdFieldName => $linkageId]
            );
        }
        if (count($toDelete)) {
            $connection->delete(
                $table,
                [$idFieldName . ' = ?' => $id, $linkIdFieldName . ' IN (?)' => $toDelete]
            );
        }

        return $this;
    }

    /**
     * Attach stores data
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function attachStores(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setStores($this->getStores($object));
        $object->setStoreId($this->getStores($object));
        return $this;
    }

    /**
     * @return string
     */
    private function getStoreLinkageTable()
    {
        return $this->getTable($this->_mainTable . '_store');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return array
     */
    private function getStores(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getStoreLinkageTable(), 'store_id')
            ->where($this->getIdFieldName() . ' = :id');
        return $connection->fetchCol($select, ['id' => $object->getId()]);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->hasUrlKey() && !$this->isUrlKeyUnique($object)) {
            throw new LocalizedException(__('This URL-Key is already assigned to another post or category.'));
        }
        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Validator\DataObject $validator
     * @return $this
     */
    protected function addUrlKeyValidateRules(\Magento\Framework\Validator\DataObject $validator)
    {
        $urlKeyNotEmpty = new \Zend_Validate_NotEmpty();
        $urlKeyNotEmpty->setMessage(__('URL-Key is required.'), \Zend_Validate_NotEmpty::IS_EMPTY);
        $validator->addRule($urlKeyNotEmpty, 'url_key');

        $urlKeyNotNumeric = new \Zend_Validate_Callback([$this, 'validateNotNumber']);
        $urlKeyNotNumeric->setMessage(
            __('URL-Key cannot consist only of numbers.'),
            \Zend_Validate_Callback::INVALID_VALUE
        );
        $validator->addRule($urlKeyNotNumeric, 'url_key');

        $urlKeyIsValid = new \Zend_Validate_Regex(['pattern' => '/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/']);
        $urlKeyIsValid->setMessage(
            __('URL-Key cannot contain capital letters or disallowed symbols.'),
            \Zend_Validate_Regex::NOT_MATCH
        );
        $validator->addRule($urlKeyIsValid, 'url_key');

        return $this;
    }

    /**
     * @param \Magento\Framework\Validator\DataObject $validator
     * @return $this
     */
    protected function addStoresValidateRules(\Magento\Framework\Validator\DataObject $validator)
    {
        $storesNotEmpty = new \Zend_Validate_NotEmpty();
        $storesNotEmpty->setMessage(__('Select store view.'), \Zend_Validate_NotEmpty::IS_EMPTY);
        $validator->addRule($storesNotEmpty, 'stores');

        return $this;
    }

    /**
     * Validate that URL-Key is unique
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function isUrlKeyUnique(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $validateTables = [
            $this->getTable('aw_blog_post'),
            $this->getTable('aw_blog_cat')
        ];
        foreach ($validateTables as $table) {
            $select = $connection->select()
                ->from($table)
                ->where('url_key = :url_key');
            $bind = ['url_key' => $object->getUrlKey()];
            if ($object->getId() && $table == $this->getMainTable()) {
                $select->where($this->getIdFieldName() . ' <> :id');
                $bind['id'] = $object->getId();
            }
            if ($connection->fetchRow($select, $bind)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate that value is not a number
     *
     * @param $value
     * @return bool
     */
    public function validateNotNumber($value)
    {
        return !preg_match('/^[0-9]+$/', $value);
    }

    /**
     * @param string $urlKey
     * @return int|null
     */
    public function getIdByUrlKey($urlKey)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('url_key = :url_key');
        $col = $connection->fetchCol($select, ['url_key' => trim($urlKey)]);
        return array_shift($col);
    }
}
