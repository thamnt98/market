<?php

namespace Wizkunde\ConfigurableBundle\Pricing;

use Magento\Bundle\Pricing\Price\BundleRegularPrice;

class RegularPrice extends BundleRegularPrice
{
    /**
     * Get price value
     *
     * @return float
     */
    public function getValue()
    {
        if ($this->value === null) {
            $price = $this->product->getPrice();

            if($price == 0) {
                $price = $this->product->getMinPrice();
            }

            $priceInCurrentCurrency = $this->priceCurrency->convertAndRound($price);
            $this->value = $priceInCurrentCurrency ? (float)$priceInCurrentCurrency : 0;
        }

        return $this->value;
    }
}
