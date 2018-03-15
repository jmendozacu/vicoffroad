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

/**
 * Class Message
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class Message extends \Magento\GiftMessage\Helper\Message
{
    /**
     * @param string                        $type
     * @param \Magento\Framework\DataObject $entity
     * @param bool|FALSE                    $dontDisplayContainer
     *
     * @return string
     */
    public function getInline($type, \Magento\Framework\DataObject $entity, $dontDisplayContainer = FALSE)
    {
        if (!$this->skipPage($type) && !$this->isMessagesAllowed($type, $entity)) {
            return '';
        }

        return $this->_layoutFactory->create()->createBlock('Magento\GiftMessage\Block\Message\Inline')
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setDontDisplayContainer($dontDisplayContainer)
            ->setEntity($entity)
            ->setCheckoutType($type)
            ->setTemplate('Magestore_OneStepCheckout::giftmessage/inline.phtml')
            ->toHtml();
    }
}