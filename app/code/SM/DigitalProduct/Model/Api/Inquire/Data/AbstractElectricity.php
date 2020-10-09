<?php
/**
 * Class AbstractElectricity
 * @package SM\DigitalProduct\Model\Api\Inquire\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Api\Inquire\Data;

use SM\DigitalProduct\Api\Inquire\Data\ResponseDataInterface;
use Magento\Framework\DataObject;

abstract class AbstractElectricity extends DataObject implements ResponseDataInterface
{
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

    /**
     * @inheritDoc
     */
    public function getAdminFee()
    {
        return $this->getData(self::ADMIN_FEE);
    }

    /**
     * @inheritDoc
     */
    public function setAdminFee($value)
    {
        return $this->setData(self::ADMIN_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }
}
