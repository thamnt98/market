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

use SM\DigitalProduct\Api\Data\Inquire\InquireElectricityDataInterface;

/**
 * Class InquireElectricityData
 * @package SM\DigitalProduct\Model\Data
 */
class InquireElectricityData extends ResponseData implements InquireElectricityDataInterface
{
    /**
     * @inheritDoc
     */
    public function setAdminCharge($value)
    {
        return $this->setData(self::ADMIN_CHARGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setStan($value)
    {
        return $this->setData(self::STAN, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDatetime($value)
    {
        return $this->setData(self::DATETIME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTerminalId($value)
    {
        return $this->setData(self::TERMINAL_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setMaterialNumber($value)
    {
        return $this->setData(self::MATERIAL_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSubscriberId($value)
    {
        return $this->setData(self::SUBSCRIBER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSwitcherRefno($value)
    {
        return $this->setData(self::SWITCHER_REFNO, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSubscriberName($value)
    {
        return $this->setData(self::SUBSCRIBER_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSubscriberSegmentation($value)
    {
        return $this->setData(self::SUBSCRIBER_SEGMENTATION, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPower($value)
    {
        return $this->setData(self::POWER, $value);
    }

    /**
     * @inheritDoc
     */
    public function setMerchantCode($value)
    {
        return $this->setData(self::MERCHANT_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBankCode($value)
    {
        return $this->setData(self::BANK_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAdminCharge()
    {
        return $this->getData(self::ADMIN_CHARGE);
    }

    /**
     * @inheritDoc
     */
    public function getStan()
    {
        return $this->getData(self::STAN);
    }

    /**
     * @inheritDoc
     */
    public function getDatetime()
    {
        return $this->getData(self::DATETIME);
    }

    /**
     * @inheritDoc
     */
    public function getTerminalId()
    {
        return $this->getData(self::TERMINAL_ID);
    }

    /**
     * @inheritDoc
     */
    public function getMaterialNumber()
    {
        return $this->getData(self::MATERIAL_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function getSubscriberId()
    {
        return $this->getData(self::SUBSCRIBER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getSwitcherRefno()
    {
        return $this->getData(self::SWITCHER_REFNO);
    }

    /**
     * @inheritDoc
     */
    public function getSubscriberName()
    {
        return $this->getData(self::SUBSCRIBER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getSubscriberSegmentation()
    {
        return $this->getData(self::SUBSCRIBER_SEGMENTATION);
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        return $this->getData(self::POWER);
    }

    /**
     * @inheritDoc
     */
    public function getMerchantCode()
    {
        return $this->getData(self::MERCHANT_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getBankCode()
    {
        return $this->getData(self::BANK_CODE);
    }
}
