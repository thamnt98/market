<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Bundle\Pricing;

/**
 * Class RegularPrice
 * @package SM\Bundle\Pricing
 */
class RegularPrice extends \Wizkunde\ConfigurableBundle\Pricing\RegularPrice
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
            $priceInCurrentCurrency = $this->priceCurrency->convertAndRound($price);
            $this->value = $priceInCurrentCurrency ? (float)$priceInCurrentCurrency : 0;
        }
        return $this->value;
    }
}
