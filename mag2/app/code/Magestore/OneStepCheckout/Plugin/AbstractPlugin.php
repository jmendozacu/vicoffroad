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
 * class AbstractPlugin
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class AbstractPlugin
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
     * AbstractPlugin constructor.
     *
     * @param Context $context
     */
    public function __construct(
        \Magestore\OneStepCheckout\Plugin\Context $context
    )
    {
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_systemConfig = $context->getSystemConfig();
        $this->_jsonHelper = $context->getJsonHelper();
        $this->_request = $context->getRequest();
        $this->_response = $context->getResponse();
        $this->_dataObjectFactory = $context->getDataObjectFactory();
        $this->_objectManager = $context->getObjectManager();
        $this->_oneStepHelper = $context->getOneStepHelper();
        $this->_storeManager = $context->getStoreManager();
        $this->_messageManager = $context->getMessageManager();
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
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
     * Get checkout url
     *
     * @return string
     */
    protected function _getCheckoutUrl()
    {
        return $this->_systemConfig->isEnableOneStepCheckout() ?
            $this->_urlBuilder->getUrl('onestepcheckout',array('_secure'=>true)) : $this->_urlBuilder->getUrl('checkout');
    }

    /**
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    public function getOnePage()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Type\Onepage');
    }
}