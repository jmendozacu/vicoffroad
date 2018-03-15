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
 * class Billing
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Billing extends AbstractAddress
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Magestore_OneStepCheckout::billing.phtml';

    /**
     * {@inheritdoc}
     */
    protected $_htmlPrefix = 'billing';

    /**
     * {@inheritdoc}
     */
    public function getFieldsMatrix()
    {
        $fieldsMatrix = $this->_systemConfig->getFieldsMatrixOption();

        if (!$this->getQuote()->isVirtual()) {
            $this->removeField($fieldsMatrix, FieldMapDataObject::FIELD_EMAIL);
        }

        return $fieldsMatrix;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteAddress()
    {
        return $this->getBillingAddress();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowPasswordSection()
    {
        return parent::canShowPasswordSection() && $this->isVirtualQuote();
    }
}