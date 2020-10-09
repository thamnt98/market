<?php
/**
 * Class DigitalTransaction
 * @package SM\DigitalProduct\Model\Cart\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Cart\Data;

use SM\DigitalProduct\Api\Data\DigitalTransactionInterface;

class DigitalTransaction extends AbstractDataObject implements DigitalTransactionInterface
{
    /**
     * @inheritDoc
     */
    public function getOperatorImage()
    {
        return $this->_get(self::OPERATOR);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setOperatorImage($value)
    {
        return $this->setData(self::OPERATOR, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSerialNumber()
    {
        /**
         * @todo remove For testing
         */
        return "0000000000000000";
        //return $this->_get(self::SERIAL_NUMBER);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSerialNumber($value)
    {
        return $this->setData(self::SERIAL_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTokenNumber()
    {
        /**
         * @todo remove For testing
         */
        return "21111111111111111";
        return $this->_get(self::TOKEN_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setTokenNumber($value)
    {
        return $this->setData(self::TOKEN_NUMBER, $value);
    }


    /**
     * @inheritDoc
     */
    public function getCustomerNumber()
    {
        return $this->_get(self::CUSTOMER_NUMBER);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomerNumber($value)
    {
        return $this->setData(self::CUSTOMER_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMeterNumber()
    {
        return $this->_get(self::METER_NUMBER);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setMeterNumber($value)
    {
        return $this->_get(self::PRODUCT_ID_VENDOR);
    }

    /**
     * @inheritDoc
     */
    public function setProductIdVendor($value)
    {
        return $this->setData(self::PRODUCT_ID_VENDOR, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProductIdVendor()
    {
        return $this->_get(self::PRODUCT_ID_VENDOR);
    }
}
