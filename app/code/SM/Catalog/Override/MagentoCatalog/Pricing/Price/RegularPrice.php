<?php

namespace SM\Catalog\Override\MagentoCatalog\Pricing\Price;

/**
 * Class RegularPrice
 * @package SM\Catalog\Override\MagentoCatalog\Pricing\Price
 */
class RegularPrice extends \Magento\Catalog\Pricing\Price\RegularPrice
{
    /**
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMinRegularAmount()
    {
        return $this->getAmount();
    }

    /**
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaxRegularAmount()
    {
        return $this->getAmount();
    }
}
