<?php
/**
 * Class DigitalTransactionInterface
 * @package SM\DigitalProduct\Api\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Data;

use SM\DigitalProduct\Model\Cart\Data\DigitalTransaction;

interface DigitalTransactionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const OPERATOR = 'operator_image';
    const SERIAL_NUMBER  = 'serial_number';
    const TOKEN_NUMBER  = 'token_number';
    const CUSTOMER_NUMBER  = 'customer_number';
    const METER_NUMBER  = 'meter_number';
    const PRODUCT_ID_VENDOR  = 'product_id_vendor';

    /**
     * @return string
     */
    public function getOperatorImage();

    /**
     * @param string $value
     * @return $this
     */
    public function setOperatorImage($value);

    /**
     * @return string
     */
    public function getSerialNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setSerialNumber($value);

    /**
     * @return string
     */
    public function getTokenNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setTokenNumber($value);

    /**
     * @return string
     */
    public function getCustomerNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomerNumber($value);

    /**
     * @return string
     */
    public function getMeterNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setMeterNumber($value);


    /**
     * @return int
     */
    public function getProductIdVendor();

    /**
     * @param int $value
     * @return $this
     */
    public function setProductIdVendor($value);
}
