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

namespace Magestore\OneStepCheckout\Controller\Coupon;

/**
 * Class Apply
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Apply extends \Magestore\OneStepCheckout\Controller\Index
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $_couponFactory;

    /**
     * Apply constructor.
     *
     * @param \Magestore\OneStepCheckout\Controller\Context $context
     * @param \Magento\Checkout\Model\Cart                  $cart
     * @param \Magento\SalesRule\Model\CouponFactory        $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface    $quoteRepository
     */
    public function __construct(
        \Magestore\OneStepCheckout\Controller\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    )
    {
        parent::__construct($context);
        $this->_cart = $cart;
        $this->_couponFactory = $couponFactory;
        $this->_quoteRepository = $quoteRepository;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        /** @var \Magento\Framework\DataObject $qtyData */
        $couponData = $this->_getParamDataObject();

        $escaper = $this->_objectManager->get('Magento\Framework\Escaper');
        $couponCode = $couponData->getData('couponCode');
        $removeCoupon = $couponData->getData('remove');
        $result = [];

        $couponCode = ($removeCoupon == 1) ? '' : trim($couponCode);

        $cartQuote = $this->_cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();

        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            $message = __('The coupon code "%1" is not valid.', $escaper->escapeHtml($couponCode));
            $result['message'] = $message;
            $result['error'] = TRUE;

            return $this->_sendResultResponse($result);
        }

        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(TRUE);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->_quoteRepository->save($cartQuote);
            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get('Magento\Framework\Escaper');
                if (!$itemsCount) {
                    if ($isCodeLengthValid) {
                        $coupon = $this->_couponFactory->create();
                        $coupon->load($couponCode, 'code');
                        if ($coupon->getId()) {
                            $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                            $message = __('You used coupon code "%1".', $escaper->escapeHtml($couponCode));
                            $result['message'] = $message;
                            $result['error'] = FALSE;

                            return $this->_sendResultResponse($result);
                        } else {
                            $message = __('The coupon code "%1" is not valid.', $escaper->escapeHtml($couponCode));
                            $result['message'] = $message;
                            $result['error'] = TRUE;

                            return $this->_sendResultResponse($result);
                        }
                    } else {
                        $message = __('The coupon code "%1" is not valid.', $escaper->escapeHtml($couponCode));
                        $result['message'] = $message;
                        $result['error'] = TRUE;

                        return $this->_sendResultResponse($result);
                    }
                } else {
                    if ($isCodeLengthValid && $couponCode == $cartQuote->getCouponCode()) {
                        $message = __('You used coupon code "%1".', $escaper->escapeHtml($couponCode));
                        $result['message'] = $message;
                        $result['error'] = FALSE;

                        return $this->_sendResultResponse($result);
                    } else {
                        $this->_cart->save();
                        $message = __('The coupon code "%1" is not valid.', $escaper->escapeHtml($couponCode));
                        $result['message'] = $message;
                        $result['error'] = TRUE;

                        return $this->_sendResultResponse($result);

                    }
                }
            } else {
                $message = __('You canceled the coupon code.');
                $result['message'] = $message;
                $result['error'] = FALSE;
                $result['cancel'] = TRUE;

                return $this->_sendResultResponse($result);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result['message'] = $e->getMessage();
            $result['error'] = TRUE;

            return $this->_sendResultResponse($result);
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            $result['error'] = TRUE;

            return $this->_sendResultResponse($result);
        }
    }

    /**
     * @param $result
     *
     * @return $this
     */
    protected function _sendResultResponse($result)
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->addHandle('onestepcheckout_handle_ajax_update');

        $result['review_info'] = $resultLayout->getLayout()->getBlock("review_info")->toHtml();
        $result['shipping_method'] = $resultLayout->getLayout()
            ->getBlock("onestepcheckout_shipping_method_available")->toHtml();

        return $this->_resultJsonFactory->create()->setData($result);
    }

}
