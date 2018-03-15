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

namespace Magestore\OneStepCheckout\Controller\Quote;

/**
 * Class DeleteItem
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class DeleteItem extends \Magestore\OneStepCheckout\Controller\Index
{
    /**
     * @var \Magento\Checkout\Model\Sidebar
     */
    protected $_sidebar;

    /**
     * DeleteItem constructor.
     *
     * @param \Magestore\OneStepCheckout\Controller\Context $context
     * @param \Magento\Checkout\Model\Sidebar               $sidebar
     */
    public function __construct(
        \Magestore\OneStepCheckout\Controller\Context $context,
        \Magento\Checkout\Model\Sidebar $sidebar
    ) {
        parent::__construct($context);
        $this->_sidebar = $sidebar;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $qtyData = $this->_dataObjectFactory->create([
            'data' => $this->_jsonHelper->jsonDecode($this->getRequest()->getContent()),
        ]);
        $itemId = $qtyData->getData('itemId');
        try {
            $this->_sidebar->checkQuoteItem($itemId);
            $this->_sidebar->removeQuoteItem($itemId);
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->notice($e->getMessage());
        }

        $isVirtual = $this->getQuote()->isVirtual();
        $quoteQty = $this->getQuote()->getItemsQty();

        if (!(int)$quoteQty) {
            $result['empty_quote'] = TRUE;
            $resultJson = $this->_resultJsonFactory->create();

            return $resultJson->setData($result);
        }

        if (!$isVirtual) {
            return $this->_getResultJson(TRUE, TRUE, TRUE, TRUE);
        } else {
            $result['is_virtual'] = TRUE;
            $resultJson = $this->_resultJsonFactory->create();

            return $resultJson->setData($result);
        }
    }

}
