<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Plugin\Ui\Model;

class Manager
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $_helper;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper
    ) {
        $this->_helper = $helper;
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

        if (array_key_exists('product_listing', $result) &&
            array_key_exists('children', $result['product_listing']) &&
            array_key_exists('listing_top', $result['product_listing']['children']) &&
            array_key_exists('children', $result['product_listing']['children']['listing_top']) &&
            array_key_exists('listing_massaction', $result['product_listing']['children']['listing_top']['children']) &&
            array_key_exists('children', $result['product_listing']['children']['listing_top']['children']['listing_massaction'])
        ) {
            $children = &$result['product_listing']['children']['listing_top']['children']['listing_massaction']['children'];
            foreach($availableActions as $item) {
                if (array_key_exists($item, $children)) {
                    continue;
                }
                $children[$item] = $this->_generateElement($item);
            }
        }
        return $result;
    }

    /*
    * Generate xml for creating one action
    * */
    protected function _generateElement($name) {
        $data = $this->_helper->getActionDataByName($name);
        $placeholder = (array_key_exists('placeholder', $data))? $data['placeholder']: '';

        $result = array(
            'arguments' => array(
                'data' => array(
                    "name" => "data",
                    "xsi:type" => "array",
                    "item" => array(
                        'config' => array(
                            "name" => "config",
                            "xsi:type" => "array",
                            "item" => array(
                                "component" => array(
                                    "name" => "component",
                                    "xsi:type" => "string",
                                    "value" => "uiComponent"
                                ),
                                "amasty_actions" => array(
                                    "name" => "component",
                                    "xsi:type" => "string",
                                    "value" => 'true'
                                ),
                                "confirm" => array(
                                    "name" => "confirm",
                                    "xsi:type" => "array",
                                    "item" => array(
                                        "title" =>array(
                                            "name" => "title",
                                            "xsi:type" => "string",
                                            "translate" => "true",
                                            "value" => $data['confirm_title']
                                        ),
                                        "message" => array(
                                            "name" => "message",
                                            "xsi:type" => "string",
                                            "translate" => "true",
                                            "value" => $data['confirm_message']
                                        )
                                    )
                                ),
                                "type" => array(
                                    "name" => "type",
                                    "xsi:type" => "string",
                                    "value" => 'amasty_' . $data['type']
                                ),
                                "label" => array(
                                    "name" => "label",
                                    "xsi:type" => "string",
                                    "translate" => "true",
                                    "value" => $data['label']
                                ),
                                "url" => array(
                                    "name" => "url",
                                    "xsi:type" => "url",
                                    "path" => $data['url']
                                )

                            )
                        )
                    )
                ),
                'actions' => array(
                    "name" => "actions",
                    "xsi:type" => "array",
                    'item' => array(
                        0 => array(
                            "name" => "0",
                            "xsi:type" => "array",
                            "item" => array(
                                "typefield" => array(
                                    "name" => "type",
                                    "xsi:type" => "string",
                                    "value" => "textbox"
                                ),
                                "fieldLabel" => array(
                                    "name" => "fieldLabel",
                                    "xsi:type" => "string",
                                    "value" => $data['fieldLabel']
                                ),
                                "placeholder" => array(
                                    "name" => "placeholder",
                                    "xsi:type" => "string",
                                    "value" => $placeholder
                                ),
                                "label" => array(
                                    "name" => "label",
                                    "xsi:type" => "string",
                                    "translate" => "true",
                                    "value" => ""
                                ),
                                "url" => array(
                                    "name" => "url",
                                    "xsi:type" => "url",
                                    "path" => $data['url']
                                ),
                                "type" => array(
                                    "name" => "type",
                                    "xsi:type" => "string",
                                    "value" => 'amasty_' . $data['type']
                                ),
                            )
                        )
                    )
                )
            ),
            'attributes'=>array(
                'class' => 'Magento\Ui\Component\Action',
                'name'  => $name
            ),
            'children'=>array()

        );

        if (array_key_exists('hide_input', $data)) {
            $result['arguments']['actions']['item'][0]['item']['hide_input'] = array(
                "name" => "hide_input",
                "xsi:type" => "string",
                "value" => '1'
            );
        }


        /*
         * his actions doesn't require input
        */
        if (strlen($name) <= 2 || $name == 'amdelete' || $name == 'removeimg') {
            unset($result['arguments']['actions']);
        }

        /*
         * this actions have select element on grid
         */
        if ( in_array($name, array('unrelate', 'unupsell', 'uncrosssell')) ) {
            $result['arguments']['actions']['item'][0]['item']['typefield']['value'] = 'select';
            $result['arguments']['actions']['item'][0]['item']['child'] = array(
                "name" => "child",
                "xsi:type" => "array",
                'item' => array(
                    0 => array(
                        "name" => "0",
                        "xsi:type" => "array",
                        "item" => array(
                            "label" => array(
                                "name" => "label",
                                "xsi:type" => "string",
                                "value" => 'Remove relations between selected products only'
                            ),
                            "fieldvalue" => array(
                                "name" => "fieldvalue",
                                "xsi:type" => "string",
                                "value" => '1'
                            ),
                        )
                    ),
                    1 => array(
                        "name" => "1",
                        "xsi:type" => "array",
                        "item" => array(
                            "label" => array(
                                "name" => "label",
                                "xsi:type" => "string",
                                "value" => 'Remove selected products from ALL relations in the catalog'
                            ),
                            "fieldvalue" => array(
                                "name" => "fieldvalue",
                                "xsi:type" => "string",
                                "value" => '2'
                            ),
                        )
                    ),
                    2 => array(
                        "name" => "2",
                        "xsi:type" => "array",
                        "item" => array(
                            "label" => array(
                                "name" => "label",
                                "xsi:type" => "string",
                                "value" => 'Remove all relations from selected products'
                            ),
                            "fieldvalue" => array(
                                "name" => "fieldvalue",
                                "xsi:type" => "string",
                                "value" => '3'
                            ),
                        )
                    )
                )
            );

        }
        return $result;
    }
}
