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

use Magestore\OneStepCheckout\Block\OneStep\Address\FieldMapDataObject;

/**
 * class Shipping
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Shipping extends AbstractAddress
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Magestore_OneStepCheckout::shipping_address.phtml';

    /**
     * {@inheritdoc}
     */
    protected $_htmlPrefix = 'shipping';

    /**
     * @return bool
     */
    public function isShowShippingAddress()
    {
        if ($this->getOnePage()->getQuote()->isVirtual()) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteAddress()
    {
        return $this->getShippingAddress();
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsMatrix()
    {
        $fieldsMatrix = $this->_systemConfig->getFieldsMatrixOption();

        /**
         * Unset field email if customer was logged in
         */
        if ($this->isCustomerLoggedIn()) {
            $this->removeField($fieldsMatrix, FieldMapDataObject::FIELD_EMAIL);
        }

        return $fieldsMatrix;
    }
}