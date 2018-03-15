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

namespace Magestore\OneStepCheckout\Plugin;

/**
 * class Context
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Context
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var \Magestore\OneStepCheckout\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magestore\OneStepCheckout\Helper\Data
     */
    protected $_oneStepHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * Context constructor.
     *
     * @param \Magestore\OneStepCheckout\Model\SystemConfig $systemConfig
     * @param \Magento\Framework\UrlInterface               $urlBuilder
     * @param \Magento\Framework\Json\Helper\Data           $jsonHelper
     * @param \Magento\Framework\App\RequestInterface       $request
     * @param \Magento\Framework\App\ResponseInterface      $response
     * @param \Magento\Framework\DataObjectFactory          $dataObjectFactory
     * @param \Magento\Framework\ObjectManagerInterface     $objectManager
     * @param \Magestore\OneStepCheckout\Helper\Data        $oneStepHelper
     * @param \Magento\Store\Model\StoreManagerInterface    $storeManager
     * @param \Magento\Framework\Message\ManagerInterface   $messageManager
     */
    public function __construct(
        \Magestore\OneStepCheckout\Model\SystemConfig $systemConfig,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\OneStepCheckout\Helper\Data $oneStepHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->_systemConfig = $systemConfig;
        $this->_jsonHelper = $jsonHelper;
        $this->_request = $request;
        $this->_response = $response;
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_objectManager = $objectManager;
        $this->_oneStepHelper = $oneStepHelper;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
    }

    /**
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getJsonHelper()
    {
        return $this->_jsonHelper;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Magento\Framework\DataObjectFactory
     */
    public function getDataObjectFactory()
    {
        return $this->_dataObjectFactory;
    }

    /**
     * @return \Magestore\OneStepCheckout\Model\SystemConfig
     */
    public function getSystemConfig()
    {
        return $this->_systemConfig;
    }

    /**
     * @return \Magento\Framework\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * @return \Magestore\OneStepCheckout\Helper\Data
     */
    public function getOneStepHelper()
    {
        return $this->_oneStepHelper;
    }

    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return \Magento\Framework\Message\ManagerInterface
     */
    public function getMessageManager()
    {
        return $this->_messageManager;
    }
}