<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_Oaction
 */
namespace Amasty\Oaction\Plugin\Ui\Model;

class Manager
{
    /**
     * @var \Amasty\Oaction\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Sales\Model\Config\Source\Order\Status
     */
    protected $orderStatus;

    public function __construct(
        \Amasty\Oaction\Helper\Data $helper,
        \Magento\Sales\Model\Config\Source\Order\Status $orderStatus
    ) {
        $this->_helper = $helper;
        $this->orderStatus = $orderStatus;
    }

    /*
     * Create xml config with php for enable\disable it from admin panel
     * */
    public function aroundGetData(
        \Magento\Ui\Model\Manager $subject,
        \Closure $proceed,
        $name
    ) {
        $result = $proceed($name);
        $availableActions = $this->_helper->getModuleConfig('general/commands');
        $availableActions = explode(',', $availableActions);

        if (array_key_exists('sales_order_grid', $result) &&
            array_key_exists('children', $result['sales_order_grid']) &&
            array_key_exists('listing_top', $result['sales_order_grid']['children']) &&
            array_key_exists('children', $result['sales_order_grid']['children']['listing_top']) &&
            array_key_exists('listing_massaction', $result['sales_order_grid']['children']['listing_top']['children']) &&
            array_key_exists('children', $result['sales_order_grid']['children']['listing_top']['children']['listing_massaction'])
        ) {
            $children = &$result['sales_order_grid']['children']['listing_top']['children']['listing_massaction']['children'];
            foreach($children as $item) {
                $name = $item['attributes']['name'];
                if (strpos( $name, 'amasty_oaction')  === false || $name == 'amasty_oaction_delemiter') {
                    continue;
                }
                $actionName = str_replace('amasty_oaction_', '', $name);
                if ($actionName == 'status'
                    && isset($item['arguments']['actions']['item'])
                    && !isset($item['arguments']['actions']['item'][0]['item']['child'])
                ) {
                    $children[$name] = $this->_addStatusValues($item);
                }
                if (in_array($actionName, $availableActions)) {
                    continue;
                }
                unset($children[$name]);
            }
        }
        return $result;
    }

    protected function _addStatusValues($item) {
        $childItem = [];
        $i = 0;
        $statuses = $this->orderStatus->toOptionArray();
        foreach($statuses as $status) {
            $childItem[] = array(
                "name" => (string) $i++,
                "xsi:type" => "array",
                "item" => array(
                    "label" => array(
                        "name" => "label",
                        "xsi:type" => "string",
                        "value" => $status['label']->render()
                    ),
                    "fieldvalue" => array(
                        "name" => "fieldvalue",
                        "xsi:type" => "string",
                        "value" => $status['value']
                    ),
                )
            );
        }

        $item['arguments']['actions']['item'][0]['item']['child'] = array(
            "name" => "child",
            "xsi:type" => "array",
            'item' => $childItem
        );

        return $item;
    }
}
