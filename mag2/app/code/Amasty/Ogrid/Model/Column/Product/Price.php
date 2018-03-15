<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */

namespace Amasty\Ogrid\Model\Column\Product;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Price extends \Amasty\Ogrid\Model\Column\Product
{
    protected $_priceFormatter;

    public function __construct(
        $fieldKey,
        $resourceModel,
        PriceCurrencyInterface $priceFormatter,
        $columns = [],
        $primaryKey = 'entity_id',
        $foreignKey = 'entity_id'
    ) {
        $this->_priceFormatter = $priceFormatter;

        parent::__construct(
            $fieldKey,
            $resourceModel,
            $columns,
            $primaryKey,
            $foreignKey
        );
    }

    public function modifyItem(&$item, $config = [])
    {
        parent::modifyItem($item, $config);

        $currencyCode = isset($config['order_currency_code']) ? $config['order_currency_code'] : null;

        $item[$this->_alias_prefix . $this->_fieldKey] = $this->_priceFormatter->format(
            $item[$this->_alias_prefix . $this->_fieldKey],
            false,
            null,
            null,
            $currencyCode
        );
    }
}