<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Helper\Category
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Helper\Category;

/**
 * Class Data
 * @package SM\DigitalProduct\Helper\Category
 */
class Data
{
    /** Type ID */
    const TOP_UP_VALUE = "topup";
    const MOBILE_PACKAGE = "mobilepackage";
    const MOBILE_PACKAGE_INTERNET_VALUE = "mobilepackage_internet";
    const BPJS_KESEHATAN_VALUE = "bpjs";
    const ELECTRICITY_TOKEN_VALUE = "electricity_token";
    const ELECTRICITY_BILL_VALUE = "electricity_bill";
    const TELKOM_POSTPAID_VALUE = "telkom";
    const PDAM_VALUE = "pdam";
    const MOBILE_POSTPAID_VALUE = "mobilepostpaid";
    const MOBILE_PACKAGE_ROAMING_VALUE = "mobilepackage_roaming";

    /** Type Title */
    const TOP_UP_LABEL = "Top Up";
    const MOBILE_PACKAGE_INTERNET_LABEL = "Mobile Package (Internet)";
    const BPJS_KESEHATAN_LABEL = "BPJS Kesehatan";
    const ELECTRICITY_TOKEN_LABEL = "PLN Electricity (Token)";
    const ELECTRICITY_BILL_LABEL = "PLN Electricity (Bill)";
    const TELKOM_POSTPAID_LABEL = "Telkom Postpaid";
    const PDAM_LABEL = "PDAM Water";
    const MOBILE_POSTPAID_LABEL = "Mobile Postpaid";
    const MOBILE_PACKAGE_ROAMING_LABEL = "Mobile Package (Roaming)";

    /**
     * @return array
     */
    public function getTypeOptions()
    {
        return [
            self::TOP_UP_VALUE => self::TOP_UP_LABEL,
            self::MOBILE_PACKAGE_INTERNET_VALUE => self::MOBILE_PACKAGE_INTERNET_LABEL,
            self::ELECTRICITY_TOKEN_VALUE => self::ELECTRICITY_TOKEN_LABEL,
            self::BPJS_KESEHATAN_VALUE => self::BPJS_KESEHATAN_LABEL,
            self::ELECTRICITY_BILL_VALUE => self::ELECTRICITY_BILL_LABEL,
            self::TELKOM_POSTPAID_VALUE => self::TELKOM_POSTPAID_LABEL,
            self::PDAM_VALUE => self::PDAM_LABEL,
            self::MOBILE_POSTPAID_VALUE => self::MOBILE_POSTPAID_LABEL,
            self::MOBILE_PACKAGE_ROAMING_VALUE => self::MOBILE_PACKAGE_ROAMING_LABEL
        ];
    }

    /**
     * @return string[]
     */
    public static function getInquireTypeList()
    {
        return [
            self::ELECTRICITY_TOKEN_VALUE => self::ELECTRICITY_TOKEN_LABEL,
            self::BPJS_KESEHATAN_VALUE => self::BPJS_KESEHATAN_LABEL,
            self::ELECTRICITY_BILL_VALUE => self::ELECTRICITY_BILL_LABEL,
            self::PDAM_VALUE => self::PDAM_LABEL,
        ];
    }
}
