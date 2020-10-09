<?php

namespace SM\DigitalProduct\Model\Data\Inquire;

use SM\DigitalProduct\Api\Data\Inquire\InquireBpjsDataInterface;

/**
 * Class InquireBpjsData
 * @package SM\DigitalProduct\Model\Data\Inquire
 */
class InquireBpjsData extends ResponseData implements InquireBpjsDataInterface
{

    /**
     * @inheritDoc
     */
    public function getTrxType()
    {
        return $this->getData(self::TRX_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getProductType()
    {
        return $this->getData(self::PRODUCT_TYPE);
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
    public function getPremi()
    {
        return $this->getData(self::PREMI);
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
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
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
    public function getMerchantCode()
    {
        return $this->getData(self::MERCHANT_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getNoVa()
    {
        return $this->getData(self::NO_VA);
    }

    /**
     * @inheritDoc
     */
    public function getNoVaKk()
    {
        return $this->getData(self::NO_VA_KK);
    }

    /**
     * @inheritDoc
     */
    public function getPeriode()
    {
        return $this->getData(self::PERIODE);
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
    public function getVaCount()
    {
        return $this->getData(self::VA_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function getKodeCabang()
    {
        return $this->getData(self::KODE_CABANG);
    }

    /**
     * @inheritDoc
     */
    public function getNamaCabang()
    {
        return $this->getData(self::NAMA_CABANG);
    }

    /**
     * @inheritDoc
     */
    public function getSisa()
    {
        return $this->getData(self::SISA);
    }

    /**
     * @inheritDoc
     */
    public function getSwReff()
    {
        return $this->getData(self::SW_REFF);
    }

    /**
     * @inheritDoc
     */
    public function getKodeLoket()
    {
        return $this->getData(self::KODE_LOKET);
    }

    /**
     * @inheritDoc
     */
    public function getAlamatLoket()
    {
        return $this->getData(self::ALAMAT_LOKET);
    }

    /**
     * @inheritDoc
     */
    public function getPhoneLoket()
    {
        return $this->getData(self::PHONE_LOKET);
    }

    /**
     * @inheritDoc
     */
    public function getKodeKabKota()
    {
        return $this->getData(self::KODE_KAB_KOTA);
    }

    /**
     * @inheritDoc
     */
    public function setTrxType($value)
    {
        return $this->setData(self::TRX_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setProductType($value)
    {
        return $this->setData(self::PRODUCT_TYPE, $value);
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
    public function setPremi($value)
    {
        return $this->setData(self::PREMI, $value);
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
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
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
    public function setMerchantCode($value)
    {
        return $this->setData(self::MERCHANT_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setNoVa($value)
    {
        return $this->setData(self::NO_VA, $value);
    }

    /**
     * @inheritDoc
     */
    public function setNoVaKk($value)
    {
        return $this->setData(self::NO_VA_KK, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPeriode($value)
    {
        return $this->setData(self::PERIODE, $value);
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
    public function setVaCount($value)
    {
        return $this->setData(self::VA_COUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setKodeCabang($value)
    {
        return $this->setData(self::KODE_CABANG, $value);
    }

    /**
     * @inheritDoc
     */
    public function setNamaCabang($value)
    {
        return $this->setData(self::NAMA_CABANG, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSisa($value)
    {
        return $this->setData(self::SISA, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSwReff($value)
    {
        return $this->setData(self::SW_REFF, $value);
    }

    /**
     * @inheritDoc
     */
    public function setKodeLoket($value)
    {
        return $this->setData(self::KODE_LOKET, $value);
    }

    /**
     * @inheritDoc
     */
    public function setAlamatLoket($value)
    {
        return $this->setData(self::ALAMAT_LOKET, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPhoneLoket($value)
    {
        return $this->setData(self::PHONE_LOKET, $value);
    }

    /**
     * @inheritDoc
     */
    public function setKodeKabKota($value)
    {
        return $this->setData(self::KODE_KAB_KOTA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getNamaLoket()
    {
        return $this->getData(self::NAMA_LOKET);
    }

    /**
     * @inheritDoc
     */
    public function setNamaLoket($value)
    {
        return $this->setData(self::NAMA_LOKET, $value);
    }
}
