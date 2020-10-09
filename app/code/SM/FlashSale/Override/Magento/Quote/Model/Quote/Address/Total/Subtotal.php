<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_FlashSale
 *
 * Date: September, 16 2020
 * Time: 3:43 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\FlashSale\Override\Magento\Quote\Model\Quote\Address\Total;

class Subtotal extends \Magento\Quote\Model\Quote\Address\Total\Subtotal
{
    /**
     * @var \SM\FlashSale\Model\Customer\Calculation
     */
    protected $flashSaleCalculation;

    /**
     * Subtotal constructor.
     *
     * @param \SM\FlashSale\Model\Customer\Calculation $flashSaleCalculation
     * @param \Magento\Quote\Model\QuoteValidator      $quoteValidator
     */
    public function __construct(
        \SM\FlashSale\Model\Customer\Calculation $flashSaleCalculation,
        \Magento\Quote\Model\QuoteValidator $quoteValidator
    ) {
        parent::__construct($quoteValidator);
        $this->flashSaleCalculation = $flashSaleCalculation;
    }

    /**
     * Processing calculation of row price for address item
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param int $finalPrice
     * @param int $originalPrice
     *
     * @return \Magento\Quote\Model\Quote\Address\Total\Subtotal
     */
    protected function _calculateRowTotal($item, $finalPrice, $originalPrice)
    {
        $finalPrice = $this->flashSaleCalculation->getFlashSalePrice($item);

        return parent::_calculateRowTotal($item, $finalPrice, $originalPrice);
    }
}
