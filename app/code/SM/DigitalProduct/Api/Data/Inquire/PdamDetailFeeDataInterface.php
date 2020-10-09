<?php

namespace SM\DigitalProduct\Api\Data\Inquire;

/**
 * Interface PdamDetailFeeDataInterface
 * @package SM\DigitalProduct\Api\Data\Inquire
 */
interface PdamDetailFeeDataInterface
{
    const PDAM_FEE = "pdam_fee";
    const MAINTENANCE_FEE = "maintenance_fee";
    const RETRIBUTION_FEE = "retribution_fee";
    const WASTEWATER_FEE = "wastewater_fee";
    const SERVICE_FEE = "service_fee";
    const STAMP_FEE = "stamp_fee";
    const RECONNECTION_FEE = "reconnection_fee";
    const NONWATER_FEE = "nonwater_fee";
    const INSTALLMENT_AMOUNT = "installment_amount";
    const SEAL_PENALTY = "seal_penalty";
    const LLTT_FEE = "lltt_fee";
    const GWT = "gwt";
    const VAT = "vat";

    /**
     * @return string
     */
    public function getPdamFee();

    /**
     * @return string
     */
    public function getMaintenanceFee();

    /**
     * @return string
     */
    public function getRetributionFee();

    /**
     * @return string
     */
    public function getWastewaterFee();

    /**
     * @return string
     */
    public function getServiceFee();

    /**
     * @return string
     */
    public function getStampFee();

    /**
     * @return string
     */
    public function getReconnectionFee();

    /**
     * @return string
     */
    public function getNonwaterFee();

    /**
     * @return string
     */
    public function getInstallmentAmount();

    /**
     * @return string
     */
    public function getSealPenalty();

    /**
     * @return string
     */
    public function getLlttFee();

    /**
     * @return string
     */
    public function getGwt();

    /**
     * @return string
     */
    public function getVat();

    /**
     * @param string $value
     * @return $this
     */
    public function setPdamFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMaintenanceFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setRetributionFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setWastewaterFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setServiceFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setStampFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setReconnectionFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setNonwaterFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setInstallmentAmount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSealPenalty($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setLlttFee($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setGwt($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setVat($value);
}
