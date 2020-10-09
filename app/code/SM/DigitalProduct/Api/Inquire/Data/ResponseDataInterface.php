<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Api\Inquire\Data;

/**
 * Interface ResponseDataInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface ResponseDataInterface
{
    const STATUS = "status";
    const RESPONSE_CODE = "response_code";
    const MESSAGE = "message";
    const ADMIN_FEE = "admin_fee";
    const PRICE = 'price';

    /**
     * @return bool
     */
    public function getStatus();

    /**
     * @param bool $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getResponseCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setResponseCode($value);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $value
     * @return $this
     */
    public function setMessage($value);

    /**
     * @return int
     */
    public function getAdminFee();

    /**
     * @param int $value
     * @return $this
     */
    public function setAdminFee($value);

    /**
     * @return int
     */
    public function getPrice();
}
