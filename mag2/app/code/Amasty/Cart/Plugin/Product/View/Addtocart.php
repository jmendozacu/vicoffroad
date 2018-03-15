<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Cart
 */
namespace Amasty\Cart\Plugin\Product\View;

class Addtocart
{
    /**
     * @var \Amasty\Cart\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    public function __construct(
        \Amasty\Cart\Helper\Data $helper,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->_helper = $helper;
        $this->layoutFactory = $layoutFactory;
    }

    public function afterToHtml(
        \Magento\Catalog\Block\Product\View $subject,
        $result
    ) {
        $name = $subject->getNameInLayout();
        $enable = $this->_helper->getModuleConfig('general/enable') && $this->_helper->getModuleConfig('general/use_product_page');

        if ( $enable && in_array($name, array('product.info.addtocart', 'product.info.addtocart.additional')) ) {
            $layout = $this->layoutFactory->create();
            $block = $layout->createBlock(
                'Amasty\Cart\Block\Config',
                'amasty.cart.config',
                [ 'data' => [] ]
            );

            $html = $block->setPageType('product')->toHtml();
            $result .= $html;
        }

        return  $result;
    }
}
