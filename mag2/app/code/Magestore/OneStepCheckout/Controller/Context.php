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
 *
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Context extends \Magento\Framework\App\Action\Context
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

    protected $_oscHelper;

    /**
     * Context constructor.
     *
     * @param \Magento\Framework\App\RequestInterface              $request
     * @param \Magento\Framework\App\ResponseInterface             $response
     * @param \Magento\Framework\ObjectManagerInterface            $objectManager
     * @param \Magento\Framework\Event\ManagerInterface            $eventManager
     * @param \Magento\Framework\UrlInterface                      $url
     * @param \Magento\Framework\App\Response\RedirectInterface    $redirect
     * @param \Magento\Framework\App\ActionFlag                    $actionFlag
     * @param \Magento\Framework\App\ViewInterface                 $view
     * @param \Magento\Framework\Message\ManagerInterface          $messageManager
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\Controller\ResultFactory          $resultFactory
     * @param \Magento\Checkout\Model\Type\Onepage                 $onePage
     * @param \Magento\Customer\Model\Session                      $customerSession
     * @param \Magento\Checkout\Model\Session                      $checkoutSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface    $customerRepository
     * @param \Magento\Customer\Api\AccountManagementInterface     $accountManagement
     * @param \Magento\Customer\Model\CustomerFactory              $customerFactory
     * @param \Magento\Framework\View\Result\LayoutFactory         $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory     $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory      $resultRawFactory
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory  $resultForwardFactory
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Framework\Json\Helper\Data                  $jsonHelper
     * @param \Magento\Framework\DataObjectFactory                 $dataObjectFactory
     * @param \Magestore\OneStepCheckout\Model\Validator           $validator
     * @param \Magestore\OneStepCheckout\Model\SystemConfig        $systemConfig
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Checkout\Model\Type\Onepage $onePage,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magestore\OneStepCheckout\Model\Validator $validator,
        \Magestore\OneStepCheckout\Helper\Data $oscHelper,
        \Magestore\OneStepCheckout\Model\SystemConfig $systemConfig
    )
    {
        parent::__construct(
            $request,
            $response,
            $objectManager,
            $eventManager,
            $url,
            $redirect,
            $actionFlag,
            $view,
            $messageManager,
            $resultRedirectFactory,
            $resultFactory
        );

        $this->_onePage = $onePage;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerRepository = $customerRepository;
        $this->_accountManagement = $accountManagement;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_resultRawFactory = $resultRawFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $registry;
        $this->_customerFactory = $customerFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_validator = $validator;
        $this->_systemConfig = $systemConfig;
        $this->_oscHelper = $oscHelper;
    }

    /**
     * @return \Magento\Framework\View\Result\LayoutFactory
     */
    public function getResultLayoutFactory()
    {
        return $this->_resultLayoutFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\JsonFactory
     */
    public function getResultJsonFactory()
    {
        return $this->_resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\RawFactory
     */
    public function getResultRawFactory()
    {
        return $this->_resultRawFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getResultPageFactory()
    {
        return $this->_resultPageFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\ForwardFactory
     */
    public function getResultForwardFactory()
    {
        return $this->_resultForwardFactory;
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function getCoreRegistry()
    {
        return $this->_coreRegistry;
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * @return \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public function getCustomerRepository()
    {
        return $this->_customerRepository;
    }

    /**
     * @return \Magento\Customer\Api\AccountManagementInterface
     */
    public function getAccountManagement()
    {
        return $this->_accountManagement;
    }

    /**
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    public function getOnePage()
    {
        return $this->_onePage;
    }

    /**
     * @return \Magento\Customer\Model\Customer\Factory
     */
    public function getCustomerFactory()
    {
        return $this->_customerFactory;
    }

    /**
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getJsonHelper()
    {
        return $this->_jsonHelper;
    }

    /**
     * @return \Magento\Framework\DataObjectFactory
     */
    public function getDataObjectFactory()
    {
        return $this->_dataObjectFactory;
    }

    /**
     * @return \Magestore\OneStepCheckout\Model\Validator
     */
    public function getValidator()
    {
        return $this->_validator;
    }

    /**
     * @return \Magestore\OneStepCheckout\Model\SystemConfig
     */
    public function getSystemConfig()
    {
        return $this->_systemConfig;
    }

    /**
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    public function getOscHelper()
    {
        return $this->_oscHelper;
    }
}