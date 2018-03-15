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

namespace Magestore\OneStepCheckout\Block\OneStep\Shipping\Method;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Rate;

/**
 * class Available
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Available extends \Magestore\OneStepCheckout\Block\AbstractOneStep
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Magestore_OneStepCheckout::shipping/available.phtml';

    /**
     * @var array
     */
    protected $_rates;

    /**
     * @var \Magento\Quote\Model\Quote\Address
     */
    protected $_address;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $_taxHelper;

    /**
     * Available constructor.
     *
     * @param \Magestore\OneStepCheckout\Block\Context $context
     * @param PriceCurrencyInterface                   $priceCurrency
     * @param \Magento\Tax\Helper\Data                 $taxHelper
     * @param array                                    $data
     */
    public function __construct(
        \Magestore\OneStepCheckout\Block\Context $context,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Tax\Helper\Data $taxHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_priceCurrency = $priceCurrency;
        $this->_taxHelper = $taxHelper;
    }

    /**
     * Get shipping rates.
     *
     * @return array
     */
    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();
            $groups = $this->getAddress()->getGroupedAllShippingRates();

            return $this->_rates = $groups;
        }

        return $this->_rates;
    }

    /**
     * @return mixed
     */
    public function getNumShippingRates()
    {
        if (!$this->hasData('num_shipping_rates')) {
            $this->setData('num_shipping_rates', count($this->getShippingRates()));
        }

        return $this->getData('num_shipping_rates');
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }

        return $this->_address;
    }

    /**
     * @param $carrierCode
     *
     * @return mixed
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = $this->_systemConfig->getCarrierName($carrierCode)) {
            return $name;
        }

        return $carrierCode;
    }

    /**
     * @return string
     */
    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    /**
     * @param $price
     */
    public function getShippingPrice($price, $flag)
    {
        return $this->_priceCurrency->convertAndFormat(
            $this->_taxHelper->getShippingPrice($price, $flag, $this->getAddress())
        );
    }

    /**
     * @return \Magento\Tax\Helper\Data
     */
    public function getTaxHelper()
    {
        return $this->_taxHelper;
    }

    /**
     * Get Excl Price
     *
     * @param Rate $shippingRate
     *
     * @return string
     */
    public function getExclPrice(Rate $shippingRate)
    {
        return $this->getShippingPrice(
            $shippingRate->getPrice(), $this->_taxHelper->displayShippingPriceIncludingTax()
        );
    }

    /**
     * Get Incl Price
     *
     * @param Rate $shippingRate
     *
     * @return string
     */
    public function getInclPrice(Rate $shippingRate)
    {
        return $this->getShippingPrice($shippingRate->getPrice(), TRUE);
    }

    /**
     * @param Rate $shippingRate
     *
     * @return bool
     */
    public function isShowBothPrices(Rate $shippingRate)
    {
        return $this->_taxHelper->displayShippingBothPrices()
        && $this->getExclPrice($shippingRate) != $this->getInclPrice($shippingRate);
    }

    /**
     * @param Rate $shippingRate
     *
     * @return bool
     */
    public function isCurrentShippingMethod(Rate $shippingRate, $code)
    {
        if ($this->getAddressShippingMethod()) {
            return $shippingRate->getCode() === $this->getAddressShippingMethod();
        } else {
            return $code === $this->_systemConfig->getDefaultShippingMethod();
        }
    }
}