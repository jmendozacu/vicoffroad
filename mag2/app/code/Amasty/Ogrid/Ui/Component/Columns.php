<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */

namespace Amasty\Ogrid\Ui\Component;

class Columns extends \Magento\Ui\Component\Container
{
    protected $_bookmarkManagement;
    protected $_helper;
    protected $_typeToFilter = [
        'text' => 'text',
        'select' => 'text',
        'multiselect' => 'text'
    ];

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Amasty\Ogrid\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->_bookmarkManagement = $bookmarkManagement;
        $this->_helper = $helper;
    }

    public function prepare()
    {

        $ret = parent::prepare();

        $columnsConfiguration = $this->getData('config');

        if (array_key_exists('productColsData', $columnsConfiguration)){
            $bookmark = $this->_bookmarkManagement->getByIdentifierNamespace(
                'current',
                'sales_order_grid'
            );
            $config = $bookmark ? $bookmark->getConfig() : null;

            $bookmarksCols = is_array($config) && isset($config['current']) && isset($config['current']['columns']) ? $config['current']['columns'] : array();

            foreach($this->getAttributeCollection() as $attribute)
            {
                $columnsConfiguration['productColsData'][$attribute->getAttributeDbAlias()] = [
                    'visible' => false,
                    'filter' => array_key_exists($attribute->getFrontendInput(), $this->_typeToFilter) ?
                        $this->_typeToFilter[$attribute->getFrontendInput()] : null,
                    'label' => $attribute->getFrontendLabel(),
                    'productAttribute' => true,
                    'frontendInput' => $attribute->getFrontendInput()
                ];
            }

            foreach($columnsConfiguration['productColsData'] as $id => &$config){
                $config['amogrid'] = array(
                    'label' => $config['label'],
                    'title' => isset($bookmarksCols[$id]) && isset($bookmarksCols[$id]['amogrid_title']) ? $bookmarksCols[$id]['amogrid_title'] : (isset($config['label']) ? $config['label'] : ''),
                    'visible' => isset($bookmarksCols[$id]) && isset($bookmarksCols[$id]['visible']) ? $bookmarksCols[$id]['visible'] : (isset($config['visible']) ? $config['visible'] : true),
                );
                $config['label'] = $config['amogrid']['title'];
            }

            $this->setData('config', $columnsConfiguration);
        }

        return $ret;
    }

    public function getAttributeCollection()
    {
        return $this->_helper->getAttributeCollection();
    }

}