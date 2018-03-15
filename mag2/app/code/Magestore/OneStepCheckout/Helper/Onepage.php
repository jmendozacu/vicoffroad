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

namespace Magestore\OneStepCheckout\Helper;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Metadata\Form;
use Magento\Checkout\Model\Type\Onepage as CheckoutOnepage;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * class Onepage
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Onepage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magestore\OneStepCheckout\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Quote\Api\GuestCartManagementInterface
     */
    protected $_cartManagement;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magestore\OneStepCheckout\Model\Validator
     */
    protected $_validator;

    /**
     * @var \Magestore\OneStepCheckout\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $_totalsCollector;

    /**
     * @var AddressRepositoryInterface
     */
    protected $_addressRepository;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_formFactory;

    /**
     * OneStep constructor.
     *
     * @param \Magento\Framework\App\Helper\Context        $context
     * @param \Magento\Framework\ObjectManagerInterface    $objectManager
     * @param \Magento\Quote\Model\Quote\TotalsCollector   $totalsCollector
     * @param AddressRepositoryInterface                   $addressRepository
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\OneStepCheckout\Helper\Data $helper,
        \Magento\Quote\Api\GuestCartManagementInterface $cartManagement,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magestore\OneStepCheckout\Model\Validator $validator,
        \Magestore\OneStepCheckout\Model\SystemConfig $systemConfig,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory
    )
    {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_cartManagement = $cartManagement;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_messageManager = $messageManager;
        $this->_logger = $context->getLogger();
        $this->_systemConfig = $systemConfig;
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
        $this->_totalsCollector = $totalsCollector;
        $this->_addressRepository = $addressRepository;
        $this->_formFactory = $formFactory;
    }

    /**
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    public function getOnePage()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Type\Onepage');
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->getOnePage()->getQuote();
    }

    /**
     * Save checkout shipping address
     *
     * @param   array $data
     * @param   int   $customerAddressId
     *
     * @return  array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            return ['error' => -1, 'message' => __('Invalid data')];
        }
        $address = $this->getQuote()->getShippingAddress();

        $addressForm = $this->_formFactory->create(
            'customer_address',
            'customer_address_edit',
            [],
            $this->_request->isAjax(),
            Form::IGNORE_INVISIBLE,
            []
        );

        if (!empty($customerAddressId)) {
            $addressData = NULL;
            try {
                $addressData = $this->_addressRepository->getById($customerAddressId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->notice($e->getMessage());
            }

            if ($addressData->getCustomerId() != $this->getQuote()->getCustomerId()) {
                return ['error' => 1, 'message' => __('The customer address is not valid.')];
            }

            $address->importCustomerAddressData($addressData)->setSaveInAddressBook(0);
            $addressErrors = $addressForm->validateData($address->getData());
            if ($addressErrors !== TRUE) {
                return ['error' => 1, 'message' => $addressErrors];
            }
        } else {
            // emulate request object
            $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
//            $addressErrors = $addressForm->validateData($addressData);
//            if ($addressErrors !== TRUE) {
//                return ['error' => 1, 'message' => $addressErrors];
//            }
            $compactedData = $addressForm->compactData($addressData);

            // unset shipping address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                $attributeCode = $attribute->getAttributeCode();
                if (!isset($data[$attributeCode])) {
                    $address->setData($attributeCode, NULL);
                } elseif (isset($compactedData[$attributeCode])) {
                    $address->setDataUsingMethod($attributeCode, $compactedData[$attributeCode]);
                }
            }

            $address->setCustomerAddressId(NULL);
            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            $address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);
        }

        $address->setCollectShippingRates(TRUE);

//        if (($validateRes = $address->validate()) !== TRUE) {
//            return ['error' => 1, 'message' => $validateRes];
//        }

        $this->_totalsCollector->collectAddressTotals($this->getQuote(), $address);
        try {
            $address->save();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
        }


        $this->getOnePage()
            ->getCheckout()
            ->setStepData('shipping', 'complete', TRUE)->setStepData('shipping_method', 'allow', TRUE);

        return [];
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
     * Register account when checkout as guest
     *
     * @param                                               $cartId
     * @param                                               $email
     * @param \Magento\Framework\DataObject                 $oneStepData
     * @param \Magento\Quote\Api\Data\AddressInterface|NULL $billingAddress
     */
    public function registerAccountAndAssignToCart(\Magento\Quote\Api\Data\CartInterface $quote) {
        if($this->isCustomerLoggedIn()) {
            return;
        }
        /** @var \Magento\Framework\DataObject $additionalData */
        $additionalData = $this->getAdditionalDataObject();

        $canRegister = $this->checkCanRegister(
            $additionalData->getData('create_account_checkbox'),
            $additionalData->getData('email'),
            $additionalData->getData('customer_password'),
            $additionalData->getData('confirm_password')
        );

        if ($canRegister) {
            $storeId = $this->_storeManager->getStore()->getId();
            $this->getOnePage()->saveCheckoutMethod(CheckoutOnepage::METHOD_REGISTER);
            try {
                $customer = $this->createCustomer(
                    $additionalData->getData('email'),
                    $quote->getBillingAddress(),
                    $additionalData
                );

                $quote->setCustomerId($customer->getId());
                $quote->setCustomerEmail($quote->getBillingAddress()->getEmail());
                $quote->setCustomerGroupId($customer->getGroupId());
                $this->_cartManagement->assignCustomer($quote->getId(), $customer->getId(), $storeId);
                $this->_customerSession->setCustomerAsLoggedIn($customer);
            } catch (\Exception $e) {
                $this->_messageManager->addError(
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                );
                $this->_logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * @param $checkCreateAccount
     * @param $email
     * @param $customerPassword
     * @param $confirmPassword
     *
     * @return bool
     */
    public function checkCanRegister($checkCreateAccount, $email, $customerPassword, $confirmPassword)
    {
        return $this->_systemConfig->enableRegistration()
        && $checkCreateAccount
        && !$this->_helper->isContainDownloadableProduct()
        && $this->_validator->validateEmail($email)
        && $this->_validator->validatePassword($customerPassword, $confirmPassword);
    }

    /**
     * @param                                          $email
     * @param                                          $password
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function createCustomer(
        $email,
        \Magento\Quote\Api\Data\AddressInterface $address,
        \Magento\Framework\DataObject $additionalData
    ){
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_customerFactory->create([
            'data' => [
                CustomerInterface::FIRSTNAME    => $address->getFirstname(),
                CustomerInterface::LASTNAME     => $address->getLastname(),
                CustomerInterface::EMAIL        => $email,
                CustomerInterface::WEBSITE_ID   => $this->_storeManager->getStore()->getWebsiteId(),
                CustomerInterface::STORE_ID     => $this->_storeManager->getStore()->getId(),
                'password'                      => $additionalData->getData('customer_password'),
                CustomerInterface::CONFIRMATION => $additionalData->getData('confirm_password'),
            ],
        ]);

        if ($address->getMiddlename()) {
            $customer->setData(CustomerInterface::MIDDLENAME, $address->getMiddlename());
        }

        if ($address->getPrefix()) {
            $customer->setData(CustomerInterface::PREFIX, $address->getPrefix());
        }

        if ($address->getSuffix()) {
            $customer->setData(CustomerInterface::SUFFIX, $address->getSuffix());
        }

        if ($address->getVatId()) {
            $customer->setData(CustomerInterface::TAXVAT, $address->getVatId());
        }

        if ($additionalData->getData(CustomerInterface::GENDER)) {
            $customer->setData(CustomerInterface::GENDER, $additionalData->getData(CustomerInterface::GENDER));
        }

        if ($additionalData->getData(CustomerInterface::DOB)) {
            $customer->setData(CustomerInterface::DOB, $additionalData->getData(CustomerInterface::DOB));
        }

        $customer->save()->sendNewAccountEmail();

        return $customer;
    }

    /**
     * Get additional data.
     *
     * @return mixed
     */
    public function getAdditionalDataObject()
    {
        $additionalData = $this->_checkoutSession->getData('additional_data');

        return $this->_dataObjectFactory->create([
            'data' => is_array($additionalData) ? $additionalData : [],
        ]);
    }
}