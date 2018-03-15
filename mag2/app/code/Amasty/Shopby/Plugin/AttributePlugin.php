<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin;


use Amasty\Shopby\Model\FilterSetting;
use Amasty\Shopby\Model\FilterSettingFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class AttributePlugin
{
    /** @var  FilterSetting */
    protected $_setting;

    public function __construct(FilterSettingFactory $settingFactory)
    {
        $this->_setting = $settingFactory->create();
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $multipleData = ['categories_filter', 'attributes_filter', 'attributes_options_filter'];

        foreach($multipleData as $multiple){
            if (array_key_exists($multiple, $data) && is_array($data[$multiple])){
                $data[$multiple] = implode(',', array_filter($data[$multiple], array($this, 'callbackNotEmpty')));
            } else if (!array_key_exists($multiple, $data)) {
                $data[$multiple] = '';
            }
        }

        return $data;
    }

    /**
     * @param $element
     * @return bool
     */
    protected function callbackNotEmpty($element)
    {
        return $element !== '';
    }

    public function aroundSave(Attribute $subject, \Closure $proceed)
    {
        if (!$subject->hasData('filter_code')) {
            return $proceed();
        }

        $filterCode = \Amasty\Shopby\Helper\FilterSetting::ATTR_PREFIX . $subject->getAttributeCode();
        $this->_setting->load($filterCode, 'filter_code');
        $data = $this->prepareData($subject->getData());
        $this->_setting->addData($data);
        $currentFilterCode = $this->_setting->getFilterCode();
        if(empty($currentFilterCode)) {
            $this->_setting->setFilterCode($filterCode);
        }

        $connection = $this->_setting->getResource()->getConnection();
        try {
            $connection->beginTransaction();
            $this->_setting->save();
            $result = $proceed();
            $connection->commit();
        } catch(\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $result;
    }
}
