<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */

namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Appendtext extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $_helper;

    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ResourceConnection $resource
     */
    protected $resource;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Config $eavConfig,
        ResourceConnection $resource
    ) {
        parent::__construct();
        $this->_helper = $helper;
        $this->objectManager = $objectManager;
        $this->resource = $resource;
        $this->_eavConfig = $eavConfig;
        $this->connection = $resource->getConnection();

        $this->_type = 'appendtext';
        $this->_info = [
            'confirm_title'   => 'Append Text',
            'confirm_message' => 'Are you sure you want to append text?',
            'type'            => 'appendtext',
            'label'           => 'Append Text',
            'fieldLabel'      => 'Append',
            'placeholder'     => 'attr.code->text'
        ];
    }

    const MODIFICATOR = '->';

    const FIELD = 'value';

    public function execute($ids, $storeId, $val)
    {
        $appendRow = $this->_generateAppend($val);
        $this->_appendText($appendRow, $ids, $storeId);

        $success = __('Total of %1 products(s) have been successfully updated.', count($ids));
        return $success;
    }

    /**
     * @param string $inputText
     * @throws Exception
     * @return array
     */
    protected function _generateAppend($inputText)
    {
        $modificatorPosition = stripos($inputText, self::MODIFICATOR);
        if (false === $modificatorPosition) {
           throw new \Amasty\Paction\Model\CustomException(__('Append field must contain: Attribute Code->Text to Append'));
        }
        $attributeCode = substr($inputText, 0, $modificatorPosition);

        $appendText = substr(
            $inputText, (strlen($attributeCode) + strlen(self::MODIFICATOR)),
            strlen($inputText)
        );

        $attributeCode = trim($attributeCode);

        return array($attributeCode, $appendText);
    }

    /**
     * @param $attributeCode
     *
     * @return \Magento\Eav\Model\Entity\Attribute
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function getAttributeByCode($attributeCode)
    {
        /** @var \Magento\Catalog\Model\Product  $model */
        $model = $this->objectManager->create('Magento\Catalog\Model\Product');
        /** @var \Magento\Eav\Model\Entity\Attribute  $attribute */
        $attribute = $model->getResource()->getAttribute($attributeCode);

        if (!$attribute) {
            throw new \Amasty\Paction\Model\CustomException(
                __('Attribute was not found by code %1.', $attributeCode));
        }
        return $attribute;
    }

    /**
     * @param array $searchReplace
     * @param array $ids
     * @param int $storeId
     *
     * @return void
     */
    protected function _appendText($searchReplace, $ids, $storeId)
    {

        list($attributeCode, $appendText) = $searchReplace;
        $attribute = $this->getAttributeByCode($attributeCode);

        $db = $this->connection;

        $set = $this->_addSetSql($db->quote($appendText), $storeId);
        $table  = $this->connection->getTableName('catalog_product_entity');

        if ($attribute->getBackendType() == 'static') {

            $sql = sprintf('UPDATE %s
              SET %s
              WHERE entity_id IN(%s)',
                $table,
                $set,
                implode(',', $ids)
            );
        } else {
            $table  = $this->connection->getTableName('catalog_product_entity_' . $attribute->getBackendType());
            $sql = sprintf('UPDATE %s
              SET %s
              WHERE attribute_id = %s
                AND entity_id IN(%s)
                AND store_id=%d',
                $table,
                $set,
                $attribute->getId(),
                implode(',', $ids),
                $storeId
            );
        }
        $db->query($sql);

    }

    /**
     * @param string $attributeCode
     * @param string $appendText
     * @param int $storeId
     *
     * @return string
     */
    protected function _addSetSql($appendText, $storeId)
    {
        $position = $this->_helper->getModuleConfig('general/append_text_position', $storeId);

        if ($position == 'before') {
            $firstPart = $appendText;
            $secondPart = self::FIELD;
        } else {
            $firstPart = self::FIELD;
            $secondPart = $appendText;
        }

        return sprintf(
            '`%s` = CONCAT(%s, %s)', self::FIELD, $firstPart, $secondPart
        );
    }

}

