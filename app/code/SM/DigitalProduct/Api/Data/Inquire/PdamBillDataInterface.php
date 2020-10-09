<?php

namespace SM\DigitalProduct\Api\Data\Inquire;

/**\
 * Interface PdamBillDataInterface
 * @package SM\DigitalProduct\Api\Data\Inquire
 */
interface PdamBillDataInterface
{
    const BILL_DATE = "bill_date";
    const KUBIKASI = "kubikasi";
    const PENALTY = "penalty";
    const BILL_AMOUNT = "bill_amount";
    const WATERUSAGE_BILL = "waterusage_bill";
    const TOTAL_FEE = "total_fee";
    const DETAIL_FEE = "detail_fee";
    const LIFT_USAGE = "lift_usage";
    const TOTAL_USAGE = "total_usage";
    const DETAIL_USAGE = "detail_usage";
    const INFO_TEXT = "info_text";

    /**
     * @return string[]
     */
    public function getBillDate();

    /**
     * @return string[]
     */
    public function getKubikasi();

    /**
     * @return string[]
     */
    public function getPenalty();

    /**
     * @return string
     */
    public function getBillAmount();

    /**
     * @return string
     */
    public function getWaterusageBill();

    /**
     * @return string
     */
    public function getTotalFee();

    /**
     * @return \SM\DigitalProduct\Api\Data\Inquire\PdamDetailFeeDataInterface
     */
    public function getDetailFee();

    /**
     * @return string
     */
    public function getLiftUsage();

    /**
     * @return string
     */
    public function getTotalUsage();

    /**
     * @return \SM\DigitalProduct\Api\Data\Inquire\PdamDetailUsageDataInterface
     */
    public function getDetailUsage();

    /**
     * @return string
     */
    public function getInfoText();

    /**
     * @param string $value
     * @return $this
     */
    public function setBillDate($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setKubikasi($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPenalty($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBillAmount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setWaterusageBill($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTotalFee($value);

    /**
     * @param \SM\DigitalProduct\Api\Data\Inquire\PdamDetailFeeDataInterface $value
     * @return $this
     */
    public function setDetailFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setLiftUsage($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTotalUsage($value);

    /**
     * @param \SM\DigitalProduct\Api\Data\Inquire\PdamDetailUsageDataInterface $value
     * @return $this
     */
    public function setDetailUsage($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setInfoText($value);
}
