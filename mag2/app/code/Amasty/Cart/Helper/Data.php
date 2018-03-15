<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Cart
 */
namespace Amasty\Cart\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_jsonEncoder = $jsonEncoder;
    }

    public function getModuleConfig($path) {
        return $this->_scopeConfig->getValue('amasty_cart/' . $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getTime()
    {
        return $this->getModuleConfig('general/time');
    }

    public function useProductPage()
    {
        return $this->getModuleConfig('general/use_product_page');
    }

    public function getProductButton()
    {
        return $this->getModuleConfig('general/product_button');
    }

    public function getDisplayAlign()
    {
        return $this->getModuleConfig('display/align');
    }

    public function displayProduct()
    {
        return $this->getModuleConfig('display/disp_product');
    }

    public function displayCount()
    {
        return $this->getModuleConfig('display/disp_count');
    }

    public function displaySumm()
    {
        return $this->getModuleConfig('display/disp_sum');
    }

    public function jsParam($block)
    {
        $param = array(
            'send_url'           =>  $this->_getUrl('amasty_cart/cart/add'),
            'src_image_progress' =>  $block->getViewFileUrl('Amasty_Cart::images/loading.gif'),
            'type_loading'       =>  $this->getModuleConfig('display/type_loading'),
            'align'              =>  $this->getDisplayAlign(),
            'linkcompare'        =>  (int)$this->getModuleConfig('general/linkcompare'),
            'wishlist'           =>  (int)$this->getModuleConfig('general/wishlist')
        );

        return $this->_jsonEncoder->encode($param);
    }

    public function getLeftButtonColor() {
        return "#" . $this->getModuleConfig('visual/left_button');
    }

    public function getRightButtonColor() {
        return "#" . $this->getModuleConfig('visual/right_button');
    }

    public function getBackgroundColor() {
        return "#" . $this->getModuleConfig('visual/background');
    }

    public function getHeaderBackgroundColor() {
        return "#" . $this->getModuleConfig('visual/header_background');
    }

    public function getTextColor() {
        return "#" . $this->getModuleConfig('visual/text');
    }

    public function getButtonTextColor() {
        return "#" . $this->getModuleConfig('visual/button_text');
    }

    public function colourBrightness($hex, $percent) {
        // Work out if hash given
        $hash = '';
        if (stristr($hex,'#')) {
            $hex = str_replace('#','',$hex);
            $hash = '#';
        }
        /// HEX TO RGB
        $rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
        //// CALCULATE
        for ($i=0; $i<3; $i++) {
            // See if brighter or darker
            if ($percent > 0) {
                // Lighter
                $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
            } else {
                // Darker
                $positivePercent = $percent - ($percent*2);
                $rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
            }
            // In case rounding up causes us to go to 256
            if ($rgb[$i] > 255) {
                $rgb[$i] = 255;
            }
        }
        //// RBG to Hex
        $hex = '';
        for($i=0; $i < 3; $i++) {
            // Convert the decimal digit to hex
            $hexDigit = dechex($rgb[$i]);
            // Add a leading zero if necessary
            if (strlen($hexDigit) == 1) {
                $hexDigit = "0" . $hexDigit;
            }
            // Append to the hex string
            $hex .= $hexDigit;
        }
        return $hash.$hex;
    }

    public function getUrl($route, $params = [])
    {
        return parent::_getUrl($route, $params);
    }
}
