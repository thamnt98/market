<?php


namespace SM\DigitalProduct\Model\Data\Inquire;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\Inquire\PdamDetailFeeDataInterface;

/**
 * Class PdamDetailFeeData
 * @package SM\DigitalProduct\Model\Data\Inquire
 */
class PdamDetailFeeData extends DataObject implements PdamDetailFeeDataInterface
{
    /**
     * @inheritDoc
     */
    public function getPdamFee()
    {
        return $this->getData(self::PDAM_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getMaintenanceFee()
    {
        return $this->getData(self::MAINTENANCE_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getRetributionFee()
    {
        return $this->getData(self::RETRIBUTION_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getWastewaterFee()
    {
        return $this->getData(self::WASTEWATER_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getServiceFee()
    {
        return $this->getData(self::SERVICE_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getStampFee()
    {
        return $this->getData(self::STAMP_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getReconnectionFee()
    {
        return $this->getData(self::RECONNECTION_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getNonwaterFee()
    {
        return $this->getData(self::NONWATER_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getInstallmentAmount()
    {
        return $this->getData(self::INSTALLMENT_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function getSealPenalty()
    {
        return $this->getData(self::SEAL_PENALTY);
    }

    /**
     * @inheritDoc
     */
    public function getLlttFee()
    {
        return $this->getData(self::LLTT_FEE);
    }

    /**
     * @inheritDoc
     */
    public function getGwt()
    {
        return $this->getData(self::GWT);
    }

    /**
     * @inheritDoc
     */
    public function getVat()
    {
        return $this->getData(self::VAT);
    }

    /**
     * @inheritDoc
     */
    public function setPdamFee($value)
    {
        return $this->setData(self::PDAM_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setMaintenanceFee($value)
    {
        return $this->setData(self::MAINTENANCE_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setRetributionFee($value)
    {
        return $this->setData(self::RETRIBUTION_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setWastewaterFee($value)
    {
        return $this->setData(self::WASTEWATER_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setServiceFee($value)
    {
        return $this->setData(self::SERVICE_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setStampFee($value)
    {
        return $this->setData(self::STAMP_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setReconnectionFee($value)
    {
        return $this->setData(self::RECONNECTION_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setNonwaterFee($value)
    {
        return $this->setData(self::NONWATER_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setInstallmentAmount($value)
    {
        return $this->setData(self::INSTALLMENT_AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSealPenalty($value)
    {
        return $this->setData(self::SEAL_PENALTY, $value);
    }

    /**
     * @inheritDoc
     */
    public function setLlttFee($value)
    {
        return $this->setData(self::LLTT_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setGwt($value)
    {
        return $this->setData(self::GWT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setVat($value)
    {
        return $this->setData(self::VAT, $value);
    }
}
