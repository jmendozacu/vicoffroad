<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Cart
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */
namespace Amasty\Cart\Block;
use Magento\Framework\Data\Form\FormKey;

class Product extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Cart\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var FormKey
     */
    protected $formKey;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        FormKey $formKey,
        array $data = [],
        \Amasty\Cart\Helper\Data $helper
    )
    {
        parent::__construct($context, $data);

        $this->_helper = $helper;
        $this->formKey = $formKey;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->imageBuilder = $imageBuilder;
        $this->setTemplate('Amasty_Cart::dialog.phtml');
    }

    public function getHelper() {
        return $this->_helper;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    public function getFormKey() {
        return $this->formKey->getFormKey();
    }
}