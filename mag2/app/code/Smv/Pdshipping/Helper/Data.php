<?php
/**
 * Copyright © 2015 Smv . All rights reserved.
 */
namespace Smv\Pdshipping\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	/**
     * @param \Magento\Framework\App\Helper\Context $context
     */
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}