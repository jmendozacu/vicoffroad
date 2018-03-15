<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   1.0.38
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\SeoObject;

class Store extends \Magento\Framework\DataObject
{
    /**
     *
     */
    public function _construct()
    {
        $this->setData($this->storeManager->getStore()->getData());
        if ($this->scopeConfig->getValue('general/store_information/name')) {
            $this->setName($this->scopeConfig->getValue('general/store_information/name'));
        }
        $this->setPhone($this->scopeConfig->getValue('general/store_information/phone'));
        $this->setAddress($this->scopeConfig->getValue('general/store_information/address'));
        $this->setEmail($this->scopeConfig->getValue('trans_email/ident_general/email'));
        $this->setUrl($this->urlManager->getBaseUrl());
    }
}
