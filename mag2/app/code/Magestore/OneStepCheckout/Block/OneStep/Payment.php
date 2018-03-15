<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_OneStepCheckout
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\OneStepCheckout\Block\OneStep;

use Magento\Payment\Model\Method\AbstractMethod;

/**
 * class Payment
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Payment extends \Magestore\OneStepCheckout\Block\AbstractOneStep
{
    protected $_template = 'Magestore_OneStepCheckout::payment_method.phtml';

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Magento\Payment\Model\Checks\SpecificationFactory
     */
    protected $_methodSpecificationFactory;


    const PAYPAL_EPRESS_ICON = 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/pp-acceptance-medium.png';

    const PAYPAL_CREDIT_ICON = 'https://www.paypalobjects.com/webstatic/en_US/i/buttons/ppc-acceptance-medium.png';

    const METHOD_WPP_BML = 'paypal_express_bml';

    const METHOD_WPP_EXPRESS = 'paypal_express';

    /**
     * Payment constructor.
     *
     * @param \Magestore\OneStepCheckout\Block\Context           $context
     * @param \Magento\Payment\Helper\Data                       $paymentHelper
     * @param \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory
     * @param array                                              $data
     */
    public function __construct(
        \Magestore\OneStepCheckout\Block\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_paymentHelper = $paymentHelper;
        $this->_methodSpecificationFactory = $methodSpecificationFactory;
    }

    /**
     * Prepare children blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /**
         * Create child blocks for payment methods forms
         */
        foreach ($this->getMethods() as $method) {
            $form = $this->_paymentHelper->getMethodFormBlock($method, $this->_layout);
            $this->_prepareMethodForm($form, $method);

            $this->setChild(
                'payment.method.' . $method->getCode(),
                $form
            );

        }

        return parent::_prepareLayout();
    }

    /**
     * @param \Magento\Framework\View\Element\Template $form
     * @param \Magento\Payment\Model\MethodInterface   $method
     *
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _prepareMethodForm(
        \Magento\Framework\View\Element\Template $form,
        \Magento\Payment\Model\MethodInterface $method
    ){
        switch($method->getCode()) {
            case self::METHOD_WPP_BML:
                $form->setData('method_title', __('PayPal Credit'));
                $form->setData(
                    'method_image',
                    sprintf('<img alt="Acceptance Mark" src="%s" class="payment-icon">', self::PAYPAL_CREDIT_ICON)
                );
                break;
            case self::METHOD_WPP_EXPRESS:
                $form->setData('method_title', __('PayPal Express Checkout'));
                $form->setData(
                    'method_image',
                    sprintf('<img alt="Acceptance Mark" src="%s" class="payment-icon">', self::PAYPAL_EPRESS_ICON)
                );
                break;
        }

        return $form;
    }

    /**
     * Check payment method model
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     *
     * @return bool
     */
    protected function _canUseMethod(\Magento\Payment\Model\MethodInterface $method)
    {
        $methodSpecification = $this->_methodSpecificationFactory->create(
            [
                AbstractMethod::CHECK_USE_FOR_COUNTRY,
                AbstractMethod::CHECK_USE_FOR_CURRENCY,
                AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
            ]
        );

        return $method && $method->canUseCheckout() && $methodSpecification->isApplicable($method, $this->getQuote());
    }

    /**
     * Check and prepare payment method model
     *
     * Redeclare this method in child classes for declaring method info instance
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     *
     * @return $this
     */
    protected function _assignMethod(\Magento\Payment\Model\MethodInterface $method)
    {
        $method->setInfoInstance($this->getQuote()->getPayment());

        return $this;
    }

    /**
     * Declare template for payment method form block
     *
     * @param string $method
     * @param string $template
     *
     * @return $this
     */
    public function setMethodFormTemplate($method = '', $template = '')
    {
        if (!empty($method) && !empty($template)) {
            if ($block = $this->getChildBlock('payment.method.' . $method)) {
                $block->setTemplate($template);
            }
        }

        return $this;
    }

    /**
     * Retrieve available payment methods
     *
     * @return \Magento\Payment\Model\MethodInterface[]
     */
    public function getMethods()
    {
        $methods = $this->getData('methods');
        if ($methods === NULL) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : NULL;
            $methods = [];
            $specification = $this->_methodSpecificationFactory->create([AbstractMethod::CHECK_ZERO_TOTAL]);
            foreach ($this->_paymentHelper->getStoreMethods($store, $quote) as $method) {
                if ($this->_canUseMethod($method) && $specification->isApplicable($method, $this->getQuote())) {
                    $this->_assignMethod($method);
                    $methods[] = $method;
                }
            }

            $this->setData('methods', $methods);
        }

        return $methods;
    }

    /**
     * @return mixed
     */
    public function getNumberMethods()
    {
        if (!$this->hasData('number_methods')) {
            $this->setData('number_methods', count($this->getMethods()));
        }

        return $this->getData('number_methods');
    }

    /**
     * Retrieve code of current payment method
     *
     * @return mixed
     */
    public function getSelectedMethodCode()
    {
        return ($method = $this->getQuote()->getPayment()->getMethod())
            ? $method : $this->_systemConfig->getDefaultPaymentMethod();
    }

    /**
     * Payment method form html getter.
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     *
     * @return string
     */
    public function getPaymentMethodFormHtml(\Magento\Payment\Model\MethodInterface $method)
    {
        return $this->getChildHtml('payment.method.' . $method->getCode());
    }

    /**
     * Return method title for payment selection page.
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     *
     * @return string
     */
    public function getMethodTitle(\Magento\Payment\Model\MethodInterface $method)
    {
        $form = $this->getChildBlock('payment.method.' . $method->getCode());
        if ($form && $form->hasMethodTitle()) {
            return $form->getMethodTitle();
        }

        return $method->getTitle();
    }

    /**
     * Return method image path for payment selection page.
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     *
     * @return string
     */
    public function getMethodImage(\Magento\Payment\Model\MethodInterface $method)
    {
        $form = $this->getChildBlock('payment.method.' . $method->getCode());
        if ($form && $form->hasMethodImage()) {
            return $form->getMethodImage();
        }

        return null;
    }

    /**
     * Payment method additional label part getter.
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     *
     * @return mixed
     */
    public function getMethodLabelAfterHtml(\Magento\Payment\Model\MethodInterface $method)
    {
        if ($form = $this->getChild('payment.method.' . $method->getCode())) {
            return $form->getMethodLabelAfterHtml();
        }
    }
}