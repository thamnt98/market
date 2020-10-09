<?php

namespace SM\DigitalProduct\Model\Data\Inquire;

use SM\DigitalProduct\Api\Data\Inquire\InquirePdamDataInterface;

/**
 * Class InquirePdamData
 * @package SM\DigitalProduct\Model\Data\Inquire
 */
class InquirePdamData extends ResponseData implements InquirePdamDataInterface
{

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
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function getTransmissionDatetime()
    {
        return $this->getData(self::TRANSMISSION_DATETIME);
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
    public function getLocalTrxTime()
    {
        return $this->getData(self::LOCAL_TRX_TIME);
    }

    /**
     * @inheritDoc
     */
    public function getLocalTrxDate()
    {
        return $this->getData(self::LOCAL_TRX_DATE);
    }

    /**
     * @inheritDoc
     */
    public function getAcquiringInstitutionId()
    {
        return $this->getData(self::ACQUIRING_INSTITUTION_ID);
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
    public function getPan()
    {
        return $this->getData(self::PAN);
    }

    /**
     * @inheritDoc
     */
    public function getProcessingCode()
    {
        return $this->getData(self::PROCESSING_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getSettlementDate()
    {
        return $this->getData(self::SETTLEMENT_DATE);
    }

    /**
     * @inheritDoc
     */
    public function getRetrievalRefNo()
    {
        return $this->getData(self::RETRIEVAL_REF_NO);
    }

    /**
     * @inheritDoc
     */
    public function getBlth()
    {
        return $this->getData(self::BLTH);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getBillCount()
    {
        return $this->getData(self::BILL_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function getBillRepeatCount()
    {
        return $this->getData(self::BILL_REPEAT_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function getRpTag()
    {
        return $this->getData(self::RP_TAG);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerAddress()
    {
        return $this->getData(self::CUSTOMER_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function getGroupCode()
    {
        return $this->getData(self::GROUP_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getGroupDesc()
    {
        return $this->getData(self::GROUP_DESC);
    }

    /**
     * @inheritDoc
     */
    public function getBills()
    {
        return $this->getData(self::BILLS);
    }

    /**
     * @inheritDoc
     */
    public function getIdpel()
    {
        return $this->getData(self::IDPEL);
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
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTransmissionDatetime($value)
    {
        return $this->setData(self::TRANSMISSION_DATETIME, $value);
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
    public function setLocalTrxTime($value)
    {
        return $this->setData(self::LOCAL_TRX_TIME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setLocalTrxDate($value)
    {
        return $this->setData(self::LOCAL_TRX_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setAcquiringInstitutionId($value)
    {
        return $this->setData(self::ACQUIRING_INSTITUTION_ID, $value);
    }

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
    public function setPan($value)
    {
        return $this->setData(self::PAN, $value);
    }

    /**
     * @inheritDoc
     */
    public function setProcessingCode($value)
    {
        return $this->setData(self::PROCESSING_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSettlementDate($value)
    {
        return $this->setData(self::SETTLEMENT_DATE . $value);
    }

    /**
     * @inheritDoc
     */
    public function setRetrievalRefNo($value)
    {
        return $this->setData(self::RETRIEVAL_REF_NO, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBlth($value)
    {
        return $this->setData(self::BLTH, $value);
    }

    /**
     * @inheritDoc
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBillCount($value)
    {
        return $this->setData(self::BILL_COUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBillRepeatCount($value)
    {
        return $this->setData(self::BILL_REPEAT_COUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setRpTag($value)
    {
        return $this->setData(self::RP_TAG, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerAddress($value)
    {
        return $this->setData(self::CUSTOMER_ADDRESS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setGroupCode($value)
    {
        return $this->setData(self::GROUP_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setGroupDesc($value)
    {
        return $this->setData(self::GROUP_DESC, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBills($value)
    {
        return $this->setData(self::BILLS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setIdpel($value)
    {
        return $this->setData(self::IDPEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMti()
    {
        return $this->getData(self::MTI);
    }

    /**
     * @inheritDoc
     */
    public function setMti($value)
    {
        return $this->setData(self::MTI, $value);
    }
}
