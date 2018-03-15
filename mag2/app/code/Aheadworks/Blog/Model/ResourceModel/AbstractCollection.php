<?php
namespace Aheadworks\Blog\Model\ResourceModel;

/**
 * Abstract collection of all blog entities
 * @package Aheadworks\Blog\Model\ResourceModel
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @param int|array $id
     * @param string $linkageTable
     * @param string $idFieldName
     * @param string $idLinkageFieldName
     * @return $this
     */
    protected function addLinkageInstanceFilter(
        $id,
        $linkageTable,
        $idFieldName,
        $idLinkageFieldName
    ) {
        $select = $this->getSelect();
        $select->joinLeft(
            $linkageTable,
            'main_table.' . $idFieldName . ' = ' . $linkageTable . '.' . $idFieldName,
            []
        );
        if (is_array($id)) {
            $select->where($linkageTable . '.' . $idLinkageFieldName . ' IN(?)', $id);
        } else {
            $select->where($linkageTable . '.' . $idLinkageFieldName . '  = ?', $id);
        }
        return $this;
    }

    /**
     * @param string $to
     * @return string
     */
    public function getLinkageTable($to)
    {
        return $this->getResource()->getMainTable() . '_' . $to;
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER)
            ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->reset(\Magento\Framework\DB\Select::GROUP);
        $countSelect->columns('COUNT(*)');

        return $countSelect;
    }
}
