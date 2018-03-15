<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Cart
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */
namespace Amasty\Cart\Block\Product;

class Related extends \Magento\Catalog\Block\Product\ProductList\Related
{
    /**
     * @var \Amasty\Cart\Helper\Data
     */
    protected $_helper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Module\Manager $moduleManager,
        \Amasty\Cart\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct(
            $context,$checkoutCart, $catalogProductVisibility, $checkoutSession, $moduleManager, $data
        );
        $this->_helper = $helper;
        $this->_scopeConfig = $context->getScopeConfig();
       // $this->setTemplate('Amasty_Cart::product/related.phtml');
    }

    public function getHelper() {
        return $this->_helper;
    }

}