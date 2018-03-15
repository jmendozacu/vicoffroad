<?php

namespace Magestore\OneStepCheckout\Block\Cart;

class Totals extends \Magento\Checkout\Block\Cart\Totals
{
    /**
     * Get unformatted in base currency base grand total value
     *
     * @return string
     */
    public function getBaseGrandTotal()
    {
        $firstTotal = reset($this->_totals);
        if ($firstTotal) {
            $total = $firstTotal->getAddress()->getBaseGrandTotal();
            return $total;
        }
        return '-';
    }
}
