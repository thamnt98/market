<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Model\Data\Inquire;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\Inquire\ResponseDataInterface;

class ResponseData extends DataObject implements ResponseDataInterface
{
    /**
     * @inheritDoc
     */
    public function getRc()
    {
        return $this->getData(self::RC);
    }

    /**
     * @inheritDoc
     */
    public function setRc($value)
    {
        return $this->setData(self::RC, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getResponseCode()
    {
        return $this->getData(self::RESPONSE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setResponseCode($value)
    {
        return $this->setData(self::RESPONSE_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTrxId($value)
    {
        return $this->setData(self::TRX_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTrxId()
    {
        return $this->getData(self::TRX_ID);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($value)
    {
        return $this->setData(self::MESSAGE, $value);
    }
}
