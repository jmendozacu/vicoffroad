<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;

class Modifyallprices extends \Amasty\Paction\Model\Command
{
    public function __construct()
    {
        parent::__construct();
        $this->_type = 'modifyallprices';
        $this->_info = [
            'confirm_title'   => 'Update All Types of Price',
            'confirm_message' => 'Are you sure you want to update all types of price?',
            'type'            => $this->_type,
            'label'           => 'Update All Types of Price',
            'fieldLabel'      => 'By',
            'placeholder'     => '+12.5, -12.5, +12.5%'
        ];
    }
        
    /**
     * Executes the command
     *
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     * @return string success message if any
     */    
    public function execute($ids, $storeId, $val)
    {
        parent::execute($ids, $storeId, $val);

        
        if (!preg_match('/^[+-][0-9]+(\.[0-9]+)?%?$/', $val)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide the difference as +12.5, -12.5, +12.5% or -12.5%'));
        }
        
        $sign = substr($val, 0, 1);
        $val  = substr($val, 1);
        
        $percent = ('%' == substr($val, -1, 1));
        if ($percent)
            $val = substr($val, 0, -1);
            
        $val = floatval($val);
        if ($val < 0.00001) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a non empty difference'));
        }

        if (!$this->_priceCodes) {
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addVisibleFilter()
                ->addFieldToFilter('frontend_input', 'price');
            $priceCodes = array();
            foreach ($attributes as $attribute) {
                $priceCodes[$attribute->getId()] = $attribute->getAttributeCode();
            }
            $this->_priceCodes = $priceCodes;
        }

        $this->_updateAttributes($ids, $storeId, array(
            'sign' => $sign, 'val' => $val, 'percent' => $percent)
        );
        
        if (version_compare(Mage::getVersion(), '1.4.1.0') > 0) {
            $process = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price');
            $process->setStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
            $process->save();
            if (Mage::helper('catalog/category_flat')->isEnabled()) {
                $process = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_flat');
                $process->setStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                $process->save();
            }
        }
        
        $success = __('Total of %1 products(s) have been successfully updated', count($ids));
        return $success; 
    }
    
    /**
     * Mass update attribute value
     *
     * @param string $attrCode attribute code, price or special price
     * @param array $productIds applied product ids
     * @param int $storeId store id
     * @param array $diff difference data (sign, value, if percentage)
     * @return bool true
     */
    protected function _updateAttributes($productIds, $storeId, $diff)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'price');

        $db    =$this->connection;
        $table = $attribute->getBackend()->getTable();

        $where = array(
            $db->quoteInto('entity_id IN(?)', $productIds),
            $db->quoteInto('attribute_id IN(?)', array_keys($this->_priceCodes)),
        );

        $value = $diff['percent'] ? '`value` * ' . $diff['val'] . '/ 100' : $diff['val'];
        $value = '`value`' . $diff['sign'] . $value; 

        if ('fixed' == Mage::getStoreConfig('ampaction/general/round', $storeId)) {
            $fixed = Mage::getStoreConfig('ampaction/general/fixed', $storeId);
            if (!empty($fixed)) {
                $fixed = floatval($fixed);
                $value = 'FLOOR(' . $value . ') + ' . $fixed;
            }
        } else { // math
            $value = 'ROUND(' . $value . ',2)'; // probably we will need change 2 to additional setting
        }

        $defaultStoreId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
        if ($storeId
            && $defaultStoreId != $storeId) {
            $storeIds = Mage::app()->getStore($storeId)->getWebsite()->getStoreIds(true);
            $where[] = $db->quoteInto('store_id IN(?)', $storeIds);
        } else { // all stores
            $storeIds = array();
            $stores = Mage::app()->getStores(true);
            foreach ($stores as $store) {
                $storeIds[] = $store->getId();
            }
            $where[] = $db->quoteInto('store_id IN(?)', $storeIds);
        }

        $where[] = 'value IS NOT NULL';

        // update all price attributes
        $sql = $this->_prepareQuery($table, $value, $where);
        $db->raw_query($sql);

        $websiteId = Mage::app()->getStore($storeId)->getWebsite()->getId();
        $where = array(
            $db->quoteInto('entity_id IN(?)', $productIds),
            $db->quoteInto('website_id = ?', $websiteId),
        );

        // update group prices
        if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
            $table = $this->connection->getTableName('catalog_product_entity_group_price');
            $sql = $this->_prepareQuery($table, $value, $where);
            $db->raw_query($sql);
        }

        // update tier prices
        $table = $this->connection->getTableName('catalog_product_entity_tier_price');
        $sql = $this->_prepareQuery($table, $value, $where);
        $db->raw_query($sql);
              
        return true;
    }

    protected function _prepareQuery($table, $value, $where)
    {
        $sql = "UPDATE $table SET `value` = $value WHERE " . join(' AND ', $where);
        return $sql;
    }
}