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
 * Interface InquireElectricityDataInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface InquireElectricityDataInterface extends ResponseDataInterface
{
    const ADMIN_CHARGE = "admin_charge";
    const STAN  = "stan";
    const DATETIME = "datetime";
    const TERMINAL_ID = "terminal_id";
    const MATERIAL_NUMBER = "material_number";
    const SUBSCRIBER_ID = "subscriber_id";
    const SWITCHER_REFNO = "switcher_refno";
    const SUBSCRIBER_NAME = "subscriber_name";
    const SUBSCRIBER_SEGMENTATION = "subscriber_segmentation";
    const POWER  = "power";
    const MERCHANT_CODE = "merchant_code";
    const BANK_CODE = "bank_code";

    /**
     * @param string $value
     * @return $this
     */
    public function setAdminCharge($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setStan($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDatetime($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTerminalId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMaterialNumber($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSubscriberId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSwitcherRefno($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSubscriberName($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSubscriberSegmentation($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPower($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBankCode($value);

    /**
     * @return string
     */
    public function getAdminCharge();

    /**
     * @return string
     */
    public function getStan();

    /**
     * @return string
     */
    public function getDatetime();

    /**
     * @return string
     */
    public function getTerminalId();

    /**
     * @return string
     */
    public function getMaterialNumber();

    /**
     * @return string
     */
    public function getSubscriberId();

    /**
     * @return string
     */
    public function getSwitcherRefno();

    /**
     * @return string
     */
    public function getSubscriberName();

    /**
     * @return string
     */
    public function getSubscriberSegmentation();

    /**
     * @return string
     */
    public function getPower();

    /**
     * @return string
     */
    public function getMerchantCode();

    /**
     * @return string
     */
    public function getBankCode();
}
