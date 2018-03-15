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

namespace Magestore\OneStepCheckout\Controller\Index;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Metadata\Form;

/**
 *
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class SaveAddressOneStep extends \Magestore\OneStepCheckout\Controller\Index
{
    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $totalsCollector;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_formFactory;

    /**
     * @var \Magestore\OneStepCheckout\Model\AddressDataExtracter
     */
    protected $_addressDataExtracter;

    /**
     * SaveAddressOneStep constructor.
     *
     * @param \Magestore\OneStepCheckout\Controller\Context         $context
     * @param \Magento\Quote\Model\Quote\TotalsCollector            $totalsCollector
     * @param AddressRepositoryInterface                            $addressRepository
     * @param \Magento\Customer\Model\Metadata\FormFactory          $formFactory
     * @param \Magestore\OneStepCheckout\Model\AddressDataExtracter $addressDataExtracter
     */
    public function __construct(
        \Magestore\OneStepCheckout\Controller\Context $context,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magestore\OneStepCheckout\Model\AddressDataExtracter $addressDataExtracter
    ) {
        parent::__construct($context);
        $this->totalsCollector = $totalsCollector;
        $this->addressRepository = $addressRepository;
        $this->_formFactory = $formFactory;
        $this->_addressDataExtracter = $addressDataExtracter;
    }

    public function setIgnoreValidation() {
        $this->getOnepage()->getQuote()->getBillingAddress()->setShouldIgnoreValidation(true);
        $this->getOnepage()->getQuote()->getShippingAddress()->setShouldIgnoreValidation(true);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */

    public function execute()
    {
        /** @var \Magento\Framework\DataObject $onestepData */
        $onestepData = $this->_getParamDataObject();

        /** @var \Magestore\OneStepCheckout\Helper\Onepage $onepageHelper */
        $onepageHelper = $this->_objectManager->get('Magestore\OneStepCheckout\Helper\Onepage');

        $this->setIgnoreValidation();

        $onepageHelper->saveShipping(
            $this->_addressDataExtracter->extractShippingAddressData($onestepData),
            $onestepData->getData('shipping_address_id')
        );

        if ($onestepData->getData('shipping_method')) {
            $this->getOnePage()->saveShippingMethod($onestepData->getData('shipping_method'));
        }

        if ($onestepData->getData('payment_method_data/method')) {
            $this->getOnePage()->savePayment($onestepData->getData('payment_method_data'));
        }

        if ($onestepData->getData('additional_data')) {
            $this->_checkoutSession->setAdditionalData($onestepData->getData('additional_data'));
        }

        $this->getOnePage()->getQuote()->collectTotals()->save();

        return $this->_getResultJson(
            $this->_systemConfig->getAjaxUpdateSectionConfig('address_review'),
            $this->_systemConfig->getAjaxUpdateSectionConfig('address_payment'),
            $this->_systemConfig->getAjaxUpdateSectionConfig('address_shipping')
        );
    }
}