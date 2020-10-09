<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Api
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Api;

use SM\DigitalProduct\Helper\Category\Data;

/**
 * Interface DigitalProductRepositoryInterface
 * @package SM\DigitalProduct\Api
 */
interface DigitalProductRepositoryInterface
{
    const SERVICE_TYPE = [
        Data::TOP_UP_VALUE => "Top Up",
        Data::MOBILE_PACKAGE_INTERNET_VALUE => "Mobile Package",
        Data::ELECTRICITY_TOKEN_VALUE => "PLN Electricity Token",
        Data::ELECTRICITY_BILL_VALUE => "PLN Electricity Bill"
    ];

    /**
     * @param string $number
     * @param string $categoryCode
     * @return \SM\DigitalProduct\Api\Data\OperatorDataInterface
     */
    public function checkPrefix($number, $categoryCode);

    /**
     * @param string $digitalCatCode
     * @return \SM\DigitalProduct\Api\Data\C1CategoryDataInterface
     */
    public function getProductsByCategory($digitalCatCode);

    /**
     * @param int $productId
     * @return string
     */
    public function getOperator($productId);
}
