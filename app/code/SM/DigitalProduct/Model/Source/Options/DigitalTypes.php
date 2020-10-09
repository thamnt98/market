<?php
/**
 * Class DigitalTypes
 * @package SM\DigitalProduct\Model\Source
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Source\Options;

use Magento\Framework\Data\OptionSourceInterface;

class DigitalTypes implements OptionSourceInterface
{

    /** Type ID */
    const TOP_UP_VALUE = "topup";
    const MOBILE_PACKAGE_VALUE = "mobilepackage";
    const ELECTRICITY_VALUE = "electricity";
    const PDAM_VALUE = "pdamwater";
    const BPJS_VALUE = "bpjs";
    const MOBILE_POSTPAID_VALUE = "mobilepostpaid";
    const TELKOM_VALUE = "telkom";

    /** Type Title */
    const TOP_UP_TITLE = "Topup";
    const MOBILE_PACKAGE_TITLE = "Mobile Package";
    const ELECTRICITY_TITLE = "PLN Electricity";
    const PDAM_TITLE = "PDAM Water";
    const BPJS_TITLE = "BPJS";
    const MOBILE_POSTPAID_TITLE = "Mobile Postpaid";
    const TELKOM_TITLE = "Telkom";

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getAllOptions() as $key => $value) {
            $options[] = [
                "value" => $key,
                "label" => $value
            ];
        }

        return $options;
    }

    /**
     * @return string[]
     */
    public function getAllOptions()
    {
        return [
            self::TOP_UP_VALUE => self::TOP_UP_TITLE,
            self::MOBILE_PACKAGE_VALUE => self::MOBILE_PACKAGE_TITLE,
            self::ELECTRICITY_VALUE => self::ELECTRICITY_TITLE,
            self::PDAM_VALUE => self::PDAM_TITLE,
            self::BPJS_VALUE => self::BPJS_TITLE,
            self::MOBILE_POSTPAID_VALUE => self::MOBILE_POSTPAID_TITLE,
            self::TELKOM_VALUE => self::TELKOM_TITLE
        ];
    }
}
