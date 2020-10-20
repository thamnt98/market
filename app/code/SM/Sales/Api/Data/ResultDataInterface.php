<?php
/**
 * @category Magento
 * @package SM\Sales\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Api\Data;

/**
 * Interface ResultInterface
 * @package SM\Sales\Api\Data
 */
interface ResultDataInterface
{
    const STATUS = "status";
    const MESSAGE = "message";

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     * @return \SM\Sales\Api\Data\ResultDataInterface
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $value
     * @return \SM\Sales\Api\Data\ResultDataInterface
     */
    public function setMessage($value);

}
