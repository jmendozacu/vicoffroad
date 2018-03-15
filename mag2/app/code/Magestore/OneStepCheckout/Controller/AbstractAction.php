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

namespace Magestore\OneStepCheckout\Controller;

/**
 * class AbstractAction
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $_onePage;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $_accountManagement;

    /**
     * @var \Magento\Customer\Model\Customer\Factory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $_resultRawFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var \Magestore\OneStepCheckout\Model\Validator
     */
    protected $_validator;

    /**
     * @var \Magestore\OneStepCheckout\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magestore\OneStepCheckout\Helper\Data
     */
    protected $_oscHelper;

    /**
     * Index constructor.
     *
     * @param ContextInterface $context
     */
    public function __construct(
        \Magestore\OneStepCheckout\Controller\Context $context
    )
    {
        parent::__construct($context);
        $this->_onePage = $context->getOnePage();
        $this->_customerSession = $context->getCustomerSession();
        $this->_checkoutSession = $context->getCheckoutSession();
        $this->_customerRepository = $context->getCustomerRepository();
        $this->_accountManagement = $context->getAccountManagement();
        $this->_customerFactory = $context->getCustomerFactory();
        $this->_resultLayoutFactory = $context->getResultLayoutFactory();
        $this->_resultJsonFactory = $context->getResultJsonFactory();
        $this->_resultRawFactory = $context->getResultRawFactory();
        $this->_resultPageFactory = $context->getResultPageFactory();
        $this->_resultForwardFactory = $context->getResultForwardFactory();
        $this->_coreRegistry = $context->getCoreRegistry();
        $this->_jsonHelper = $context->getJsonHelper();
        $this->_dataObjectFactory = $context->getDataObjectFactory();
        $this->_validator = $context->getValidator();
        $this->_systemConfig = $context->getSystemConfig();
        $this->_oscHelper = $context->getOscHelper();
    }

    /**
     * Get logged in customer.
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = $this->_customerSession->getCustomer();
        }

        return $this->_customer;
    }

    /**
     * Check customer is loggedin.
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    public function getOnePage()
    {
        return $this->_onePage;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->getOnePage()->getQuote();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Get param object from ajax request
     *
     * @return \Magento\Framework\DataObject
     */
    protected function _getParamDataObject()
    {
        return $this->_dataObjectFactory->create([
            'data' => $this->_jsonHelper->jsonDecode($this->getRequest()->getContent()),
        ]);
    }

    /**
     * @param bool $reviewFlag
     * @param bool $paymentMethodFlag
     * @param bool $shippingMethodFlag
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function _getResultJson(
        $reviewFlag = FALSE,
        $paymentMethodFlag = FALSE,
        $shippingMethodFlag = FALSE,
        $giftWrapAmount = FALSE
    )
    {
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->addHandle('onestepcheckout_handle_ajax_update');
        $result = [];

        if ($reviewFlag) {
            $result['review_info'] = $resultLayout->getLayout()->getBlock('review_info')->toHtml();
        }

        if ($shippingMethodFlag) {
            $result['shipping_method'] = $resultLayout->getLayout()
                ->getBlock('onestepcheckout_shipping_method_available')->toHtml();
        }

        if ($paymentMethodFlag) {
            $result['payment_method'] = true;
        }

        if ($giftWrapAmount) {
            $result['giftWrap_amount'] = $this->_objectManager
                ->get('Magento\Checkout\Helper\Data')->formatPrice($this->_oscHelper->getOrderGiftWrapAmount());
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_resultJsonFactory->create();

        return $resultJson->setData($result);
    }


}
