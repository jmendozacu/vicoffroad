<?php
namespace Drc\EcommerceTracking\Helper;

class Analytics extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_ENABLE = 'googletagmanager/analytics/enable';
    const XML_PATH_ANALYTICS = 'googletagmanager/analytics/track_id';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * Whether Analytics is ready to use
     *
     * @return bool
     */
    public function isEnabled()
    {
        $trackId = $this->scopeConfig->getValue(self::XML_PATH_ANALYTICS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $trackId && $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Analytics Track ID
     *
     * @return bool | null | string
     */
    public function getTrackId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ANALYTICS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
