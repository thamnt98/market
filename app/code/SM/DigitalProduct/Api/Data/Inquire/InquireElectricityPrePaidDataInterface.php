<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Api\Data\Inquire;

/**
 * Interface InquireElectricityPrePaidDataInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface InquireElectricityPrePaidDataInterface extends InquireElectricityDataInterface
{
    const PLN_REFNO = "pln_refno";
    const DISTRIBUTION_CODE = "distribution_code";
    const SERVICE_UNIT = "service_unit";
    const SERVICE_UNIT_PHONE = "service_unit_phone";
    const MAX_KWH_UNIT = "max_kwh_unit";
    const TOTAL_REPEAT = "total_repeat";
    const POWER_PURCHASE_UNSOLD = "power_purchase_unsold";
    const POWER_PURCHASE_UNSOLD2 = "power_purchase_unsold2";

    /**
     * @param string $value
     * @return $this
     */
    public function setPlnRefno($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDistributionCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setServiceUnit($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setServiceUnitPhone($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMaxKwhUnit($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTotalRepeat($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPowerPurchaseUnsold($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPowerPurchaseUnsold2($value);

    /**
     * @return string
     */
    public function getPlnRefno();

    /**
     * @return string
     */
    public function getDistributionCode();

    /**
     * @return string
     */
    public function getServiceUnit();

    /**
     * @return string
     */
    public function getServiceUnitPhone();

    /**
     * @return string
     */
    public function getMaxKwhUnit();

    /**
     * @return string
     */
    public function getTotalRepeat();

    /**
     * @return string
     */
    public function getPowerPurchaseUnsold();

    /**
     * @return string
     */
    public function getPowerPurchaseUnsold2();
}
