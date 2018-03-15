<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
* @package Amasty_Oaction
*/
namespace Amasty\Oaction\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->date = $date;
        $this->_scopeConfig = $context->getScopeConfig();

    }

    /**
     * Get module setting value.
     * @return string
     */
    public function getModuleConfig($path, $store = 0) {
        return $this->_scopeConfig->getValue(
            'amasty_oaction/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getDate()
    {
        return $this->date->gmtDate();
    }
}