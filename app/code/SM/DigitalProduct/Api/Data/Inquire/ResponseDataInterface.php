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
 * Interface ResponseDataInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface ResponseDataInterface
{
    const RC = "rc";
    const STATUS = "status";
    const RESPONSE_CODE = "response_code";
    const TRX_ID = "trx_id";
    const MESSAGE = "message";

    /**
     * @return string
     */
    public function getRc();

    /**
     * @param string $value
     * @return $this
     */
    public function setRc($value);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $value
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
     * @param string $value
     * @return $this
     */
    public function setTrxId($value);

    /**
     * @return string
     */
    public function getTrxId();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $value
     * @return $this
     */
    public function setMessage($value);
}
