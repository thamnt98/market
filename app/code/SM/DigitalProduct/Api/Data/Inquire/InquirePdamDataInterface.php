<?php

namespace SM\DigitalProduct\Api\Data\Inquire;

/**
 * Interface InquirePdamDataInterface
 * @package SM\DigitalProduct\Api\Data\Inquire
 */
interface InquirePdamDataInterface extends ResponseDataInterface
{
    const STAN = "stan";
    const AMOUNT = "amount";
    const TRANSMISSION_DATETIME = "transmission_datetime";
    const MERCHANT_CODE = "merchant_code";
    const LOCAL_TRX_TIME = "local_trx_time";
    const LOCAL_TRX_DATE = "local_trx_date";
    const ACQUIRING_INSTITUTION_ID = "acquiring_institution_id";
    const ADMIN_CHARGE = "admin_charge";
    const MTI = "mti";
    const PAN = "pan";
    const PROCESSING_CODE = "processing_code";
    const SETTLEMENT_DATE = "settlement_date";
    const RETRIEVAL_REF_NO = "retrieval_ref_no";
    const BLTH = "blth";
    const NAME = "name";
    const BILL_COUNT = "bill_count";
    const BILL_REPEAT_COUNT = "bill_repeat_count";
    const RP_TAG = "rp_tag";
    const CUSTOMER_ADDRESS = "customer_address";
    const GROUP_CODE = "group_code";
    const GROUP_DESC = "group_desc";
    const BILLS = "bills";
    const IDPEL = "idpel";

    /**
     * @return string
     */
    public function getStan();

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @return string
     */
    public function getTransmissionDatetime();

    /**
     * @return string
     */
    public function getMerchantCode();

    /**
     * @return string
     */
    public function getLocalTrxTime();

    /**
     * @return string
     */
    public function getLocalTrxDate();

    /**
     * @return string
     */
    public function getAcquiringInstitutionId();

    /**
     * @return string
     */
    public function getAdminCharge();

    /**
     * @return string
     */
    public function getMti();

    /**
     * @return string
     */
    public function getPan();

    /**
     * @return string
     */
    public function getProcessingCode();

    /**
     * @return string
     */
    public function getSettlementDate();

    /**
     * @return string
     */
    public function getRetrievalRefNo();

    /**
     * @return string
     */
    public function getBlth();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getBillCount();

    /**
     * @return string
     */
    public function getBillRepeatCount();

    /**
     * @return string
     */
    public function getRpTag();

    /**
     * @return string
     */
    public function getCustomerAddress();

    /**
     * @return string
     */
    public function getGroupCode();

    /**
     * @return string
     */
    public function getGroupDesc();

    /**
     * @return \SM\DigitalProduct\Api\Data\Inquire\PdamBillDataInterface[]
     */
    public function getBills();

    /**
     * @return string
     */
    public function getIdpel();

    /**
     * @param string $value
     * @return $this
     */
    public function setStan($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setAmount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTransmissionDatetime($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setLocalTrxTime($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setLocalTrxDate($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setAcquiringInstitutionId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setAdminCharge($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMti($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPan($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setProcessingCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSettlementDate($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setRetrievalRefNo($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBlth($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBillCount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBillRepeatCount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setRpTag($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomerAddress($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setGroupCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setGroupDesc($value);

    /**
     * @param \SM\DigitalProduct\Api\Data\Inquire\PdamBillDataInterface[] $value
     * @return $this
     */
    public function setBills($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setIdpel($value);
}
