<?php
namespace Eway\EwayRapid\Model\Method;

use Eway\EwayRapid\Model\Config\Source\Mode;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\PaymentException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;

class Adapter implements MethodInterface
{
    /** @var MethodInterface[] */
    protected $methodInstances = []; // @codingStandardsIgnoreLine

    /** @var MethodInterface  */
    protected $methodInstance = null; // @codingStandardsIgnoreLine

    /** @var ConfigInterface */
    protected $config; // @codingStandardsIgnoreLine

    /** @var ObjectManagerInterface */
    protected $objectManager; // @codingStandardsIgnoreLine

    public function __construct(
        ConfigInterface $config,
        ObjectManagerInterface $objectManager,
        $methodInstances = []
    ) {
    
        $this->config = $config;
        $this->methodInstances = $methodInstances;
        $this->objectManager = $objectManager;
    }

    protected function getMethodInstance() // @codingStandardsIgnoreLine
    {
        if ($this->methodInstance == null) {
            $connectionType = $this->config->getValue('connection_type');
            if (isset($this->methodInstances[$connectionType])) {
                // @codingStandardsIgnoreLine We don't want to create all method instance objects
                $this->methodInstance = $this->objectManager->get($this->methodInstances[$connectionType]);
            } else {
                throw new PaymentException(__('Connection type is not set.'));
            }
        }

        return $this->methodInstance;
    }

    /**
     * Retrieve payment method code
     *
     * @return string
     *
     */
    public function getCode()
    {
        return $this->getMethodInstance()->getCode();
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     *
     * @deprecated
     */
    public function getFormBlockType()
    {
        return $this->getMethodInstance()->getFormBlockType();
    }

    /**
     * Retrieve payment method title
     *
     * @return string
     *
     */
    public function getTitle()
    {
        return $this->getMethodInstance()->getTitle();
    }

    /**
     * Store id setter
     * @param int $storeId
     * @return self
     */
    public function setStore($storeId)
    {
        $this->getMethodInstance()->setStore($storeId);
        return $this;
    }

    /**
     * Store id getter
     * @return int
     */
    public function getStore()
    {
        return $this->getMethodInstance()->getStore();
    }

    /**
     * Check order availability
     *
     * @return bool
     *
     */
    public function canOrder()
    {
        return $this->getMethodInstance()->canOrder();
    }

    /**
     * Check authorize availability
     *
     * @return bool
     *
     */
    public function canAuthorize()
    {
        return $this->getMethodInstance()->canAuthorize();
    }

    /**
     * Check capture availability
     *
     * @return bool
     *
     */
    public function canCapture()
    {
        return $this->getMethodInstance()->canCapture();
    }

    /**
     * Check partial capture availability
     *
     * @return bool
     *
     */
    public function canCapturePartial()
    {
        return $this->getMethodInstance()->canCapturePartial();
    }

    /**
     * Check whether capture can be performed once and no further capture possible
     *
     * @return bool
     *
     */
    public function canCaptureOnce()
    {
        return $this->getMethodInstance()->canCaptureOnce();
    }

    /**
     * Check refund availability
     *
     * @return bool
     *
     */
    public function canRefund()
    {
        return $this->getMethodInstance()->canRefund();
    }

    /**
     * Check partial refund availability for invoice
     *
     * @return bool
     *
     */
    public function canRefundPartialPerInvoice()
    {
        return $this->getMethodInstance()->canRefundPartialPerInvoice();
    }

    /**
     * Check void availability
     * @return bool
     *
     */
    public function canVoid()
    {
        return $this->getMethodInstance()->canVoid();
    }

    /**
     * Using internal pages for input payment data
     * Can be used in admin
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return $this->getMethodInstance()->canUseInternal();
    }

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        return $this->getMethodInstance()->canUseCheckout();
    }

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     *
     */
    public function canEdit()
    {
        return $this->getMethodInstance()->canEdit();
    }

    /**
     * Check fetch transaction info availability
     *
     * @return bool
     *
     */
    public function canFetchTransactionInfo()
    {
        return $this->getMethodInstance()->canFetchTransactionInfo();
    }

    /**
     * Fetch transaction info
     *
     * @param InfoInterface $payment
     * @param string $transactionId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     */
    public function fetchTransactionInfo(InfoInterface $payment, $transactionId)
    {
        return $this->getMethodInstance()->fetchTransactionInfo($payment, $transactionId);
    }

    /**
     * Retrieve payment system relation flag
     *
     * @return bool
     *
     */
    public function isGateway()
    {
        return $this->getMethodInstance()->isGateway();
    }

    /**
     * Retrieve payment method online/offline flag
     *
     * @return bool
     *
     */
    public function isOffline()
    {
        return $this->getMethodInstance()->isOffline();
    }

    /**
     * Flag if we need to run payment initialize while order place
     *
     * @return bool
     *
     */
    public function isInitializeNeeded()
    {
        return $this->getMethodInstance()->isInitializeNeeded();
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @param string $country
     * @return bool
     */
    public function canUseForCountry($country)
    {
        return $this->getMethodInstance()->canUseForCountry($country);
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->getMethodInstance()->canUseForCurrency($currencyCode);
    }

    /**
     * Retrieve block type for display method information
     *
     * @return string
     *
     * @deprecated
     */
    public function getInfoBlockType()
    {
        return $this->getMethodInstance()->getInfoBlockType();
    }

    /**
     * Retrieve payment information model object
     *
     * @return InfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @deprecated
     */
    public function getInfoInstance()
    {
        return $this->getMethodInstance()->getInfoInstance();
    }

    /**
     * Retrieve payment information model object
     *
     * @param InfoInterface $info
     * @return self
     *
     * @deprecated
     */
    public function setInfoInstance(InfoInterface $info)
    {
        $this->getMethodInstance()->setInfoInstance($info);
        return $this;
    }

    /**
     * Validate payment method information object
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function validate()
    {
        return $this->getMethodInstance()->validate();
    }

    /**
     * Order payment method
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->getMethodInstance()->order($payment, $amount);
    }

    /**
     * Authorize payment method
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->getMethodInstance()->authorize($payment, $amount);
    }

    /**
     * Capture payment method
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->getMethodInstance()->capture($payment, $amount);
    }

    /**
     * Refund specified amount for payment
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     *
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->getMethodInstance()->refund($payment, $amount);
    }

    /**
     * Cancel payment method
     *
     * @param InfoInterface $payment
     * @return $this
     *
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->getMethodInstance()->cancel($payment);
    }

    /**
     * Void payment method
     *
     * @param InfoInterface $payment
     * @return $this
     *
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->getMethodInstance()->void($payment);
    }

    /**
     * Whether this method can accept or deny payment
     * @return bool
     *
     */
    public function canReviewPayment()
    {
        return $this->getMethodInstance()->canReviewPayment();
    }

    /**
     * Attempt to accept a payment that us under review
     *
     * @param InfoInterface $payment
     * @return false
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function acceptPayment(InfoInterface $payment)
    {
        return $this->getMethodInstance()->acceptPayment($payment);
    }

    /**
     * Attempt to deny a payment that us under review
     *
     * @param InfoInterface $payment
     * @return false
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     */
    public function denyPayment(InfoInterface $payment)
    {
        return $this->getMethodInstance()->denyPayment($payment);
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|\Magento\Store\Model\Store $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->getMethodInstance()->getConfigData($field, $storeId);
    }

    /**
     * Assign data to info model instance
     *
     * @param DataObject $data
     * @return $this
     *
     */
    public function assignData(DataObject $data)
    {
        return $this->getMethodInstance()->assignData($data);
    }

    /**
     * Check whether payment method can be used
     *
     * @param CartInterface|null $quote
     * @return bool
     *
     */
    public function isAvailable(CartInterface $quote = null)
    {
        $storeId = $quote ? $quote->getStoreId() : null;
        $result = $this->config->getValue('mode', $storeId) == Mode::SANDBOX ?
            (!empty($this->config->getValue('sandbox_api_key', $storeId))
                && !empty($this->config->getValue('sandbox_api_password', $storeId))) :
            (!empty($this->config->getValue('live_api_key', $storeId))
                && !empty($this->config->getValue('live_api_password', $storeId)));

        return $result && $this->getMethodInstance()->isAvailable($quote);
    }

    /**
     * Is active
     *
     * @param int|null $storeId
     * @return bool
     *
     */
    public function isActive($storeId = null)
    {
        return $this->getMethodInstance()->isActive($storeId);
    }

    /**
     * Method that will be executed instead of authorize or capture
     * if flag isInitializeNeeded set to true
     *
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     */
    public function initialize($paymentAction, $stateObject)
    {
        return $this->getMethodInstance()->initialize($paymentAction, $stateObject);
    }

    /**
     * Get config payment action url
     * Used to universalize payment actions when processing payment place
     *
     * @return string
     *
     */
    public function getConfigPaymentAction()
    {
        return $this->getMethodInstance()->getConfigPaymentAction();
    }
}
