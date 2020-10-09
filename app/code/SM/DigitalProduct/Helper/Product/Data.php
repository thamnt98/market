<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Helper\Product
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Helper\Product;

/**
 * Class Data
 * @package SM\DigitalProduct\Helper\Product
 */
class Data
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * Data constructor.
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->priceHelper = $priceHelper;
    }

    /**
     * @param float $value
     * @return float|string
     */
    public function currencyFormat($value)
    {
        return $this->priceHelper->currency($value, true, false);
    }
}
