<?php
/**
 * @category Magento
 * @package SM\Sales\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Model\Data;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\PaymentInfoDataInterface;

/**
 * Class PaymentInfoData
 * @package SM\Sales\Model\Data
 */
class PaymentInfoData extends DataObject implements PaymentInfoDataInterface
{
    /**
     * @inheritDoc
     */
    public function setMethod($value)
    {
        return $this->setData(self::METHOD, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return $this->getData(self::METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setBankIssuer($value)
    {
        return $this->setData(self::BANK_ISSUER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getBankIssuer()
    {
        return $this->getData(self::BANK_ISSUER);
    }

    /**
     * @inheritDoc
     */
    public function setCardBrand($value)
    {
        return $this->setData(self::CARD_BRAND, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCardBrand()
    {
        return $this->getData(self::CARD_BRAND);
    }

    /**
     * @inheritDoc
     */
    public function setCardType($value)
    {
        return $this->setData(self::CARD_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCardType()
    {
        return $this->getData(self::CARD_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setCardNo($value)
    {
        return $this->setData(self::CARD_NO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCardNo()
    {
        return $this->getData(self::CARD_NO);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionId($value)
    {
        return $this->setData(self::TRANSACTION_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getExpireDate()
    {
        return $this->getData(self::EXPIRE_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setExpireDate($value)
    {
        return $this->setData(self::EXPIRE_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getVirtualAccount()
    {
        return $this->getData(self::VIRTUAL_ACCOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setVirtualAccount($value)
    {
        return $this->setData(self::VIRTUAL_ACCOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLogo()
    {
        return $this->getData(self::LOGO);
    }

    /**
     * @inheritDoc
     */
    public function setLogo($value)
    {
        return $this->setData(self::LOGO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl()
    {
        return $this->getData(self::REDIRECT_URL);
    }

    /**
     * @inheritDoc
     */
    public function setRedirectUrl($value)
    {
        return $this->setData(self::REDIRECT_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getHowToPay()
    {
        return $this->getData(self::HOW_TO_PAY);
    }

    /**
     * @inheritDoc
     */
    public function setHowToPay($value)
    {
        return $this->setData(self::HOW_TO_PAY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getHowToPayObjects()
    {
        return $this->getData(self::HOW_TO_PAY_OBJECTS);
    }

    /**
     * @inheritDoc
     */
    public function setHowToPayObjects($value)
    {
        return $this->setData(self::HOW_TO_PAY_OBJECTS, $value);
    }
}
