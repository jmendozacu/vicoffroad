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
 * @package     Magestore_Shopbybrand
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Shopbybrand\Observer\Catalog;

use Magento\Framework\Event\Observer;

/**
 *
 *
 * @category Magestore
 * @package  Magestore_Shopbybrand
 * @module   Shopbybrand
 * @author   Magestore Developer
 */
class BlockProductListCollection extends \Magestore\Shopbybrand\Observer\AbstractObserver
{
    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection */
        $collection = $observer->getData('collection');

        if (!$this->_coreRegistry->registry('is_join_position')) {
            $route = $this->getRequest()->getRouteName();
            $params = $this->getRequest()->getParams();
            if (isset($params['brand_id'])) {
                if (!isset($params['order'])) {
                    $params['order'] = '';
                }
                if (($route == 'shopbybrand') && ($params['order'] == null || $params['order'] == 'position')) {
                    if (!isset($params['dir'])) {
                        $params['dir'] = '';
                    }
                    $dir = ($params['dir'] != 'desc') ? 'asc' : 'desc';
                    $collection
                        ->getSelect()
                        ->joinLeft(
                            ['brand_products' => $collection->getTable('brand_products')],
                            "e.entity_id = brand_products.product_id",
                            [
                                'position' => 'brand_products.position',
                            ]
                        )
                        ->order('brand_products.position ' . $dir);
                }
            }

            $this->_coreRegistry->register('is_join_position', '0');
        }
    }
}