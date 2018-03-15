<?php
namespace Drc\EcommerceTracking\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cookie\Helper\Cookie as CookieHelper;
use Drc\EcommerceTracking\Helper\Analytics as GAHelper;

/**
 * Google Tag Manager Page Block
 */
class Analytics extends Template
{

    /**
     * Google Tag Manager data
     *
     * @var Drc\EcommerceTracking\Helper\Data
     */
    protected $_gaHelper = null;

    /**
     * Cookie Helper
     *
     * @var \Magento\Cookie\Helper\Cookie
     */
    protected $_cookieHelper = null;

    /**
     * @param Context $context
     * @param CookieHelper $cookieHelper
     * @param GtmHelper $gtmHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CookieHelper $cookieHelper,
        GAHelper $gaHelper,
        array $data = []
    ) {
        $this->_cookieHelper = $cookieHelper;
        $this->_gaHelper = $gaHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Account Id
     *
     * @return string
     */
    public function getTrackId()
    {
        return $this->_gaHelper->getTrackId();
    }

    /**
     * Render tag manager JS
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_gaHelper->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }
}
