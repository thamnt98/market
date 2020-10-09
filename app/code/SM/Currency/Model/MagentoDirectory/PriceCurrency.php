<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Currency
 *
 * Date: June, 23 2020
 * Time: 2:09 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Currency\Model\MagentoDirectory;

class PriceCurrency extends \Magento\Directory\Model\PriceCurrency
{
    const DEFAULT_PRECISION = 0;

    /**
     * @override
     *
     * @param float $price
     *
     * @return float
     */
    public function round($price)
    {
        return round($price);
    }

    /**
     * @override
     *
     * @param      $amount
     * @param null $scope
     * @param null $currency
     * @param int  $precision
     *
     * @return float
     */
    public function convertAndRound($amount, $scope = null, $currency = null, $precision = self::DEFAULT_PRECISION)
    {
        return parent::convertAndRound($amount, $scope, $currency, $precision);
    }

    /**
     * @override
     *
     * Round price with precision
     *
     * @param float $price
     * @param int   $precision
     *
     * @return float
     */
    public function roundPrice($price, $precision = self::DEFAULT_PRECISION)
    {
        return round($price, $precision);
    }
}
