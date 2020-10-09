<?php

namespace SM\DigitalProduct\Api\Data\Inquire;

/**
 * Interface InquireBpjsDataInterface
 * @package SM\DigitalProduct\Api\Data\Inquire
 */
interface InquireBpjsDataInterface extends ResponseDataInterface
{
    const TRX_TYPE = "trx_type";
    const PRODUCT_TYPE = "product_type";
    const STAN = "stan";
    const PREMI = "premi";
    const ADMIN_CHARGE = "admin_charge";
    const AMOUNT = "amount";
    const DATETIME = "datetime";
    const MERCHANT_CODE = "merchant_code";
    const NO_VA = "no_va";
    const NO_VA_KK = "no_va_kk";
    const PERIODE = "periode";
    const NAME = "name";
    const VA_COUNT = "va_count";
    const KODE_CABANG = "kode_cabang";
    const NAMA_CABANG = "nama_cabang";
    const SISA = "sisa";
    const SW_REFF = "sw_reff";
    const KODE_LOKET = "kode_loket";
    const NAMA_LOKET = "nama_loket";
    const ALAMAT_LOKET = "alamat_loket";
    const PHONE_LOKET = "phone_loket";
    const KODE_KAB_KOTA = "kode_kab_kota";

    /**
     * @return string
     */
    public function getTrxType();

    /**
     * @return string
     */
    public function getProductType();

    /**
     * @return string
     */
    public function getStan();

    /**
     * @return string
     */
    public function getPremi();

    /**
     * @return string
     */
    public function getAdminCharge();

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @return string
     */
    public function getDatetime();

    /**
     * @return string
     */
    public function getMerchantCode();

    /**
     * @return string
     */
    public function getNoVa();

    /**
     * @return string
     */
    public function getNoVaKk();

    /**
     * @return string
     */
    public function getPeriode();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getVaCount();

    /**
     * @return string
     */
    public function getKodeCabang();

    /**
     * @return string
     */
    public function getNamaCabang();

    /**
     * @return string
     */
    public function getSisa();

    /**
     * @return string
     */
    public function getSwReff();

    /**
     * @return string
     */
    public function getKodeLoket();

    /**
     * @return string
     */
    public function getNamaLoket();

    /**
     * @return string
     */
    public function getAlamatLoket();

    /**
     * @return string
     */
    public function getPhoneLoket();

    /**
     * @return string
     */
    public function getKodeKabKota();

    /**
     * @param string $value
     * @return $this
     */
    public function setTrxType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setProductType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setStan($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPremi($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setAdminCharge($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setAmount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDatetime($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setNoVa($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setNoVaKk($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPeriode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setVaCount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setKodeCabang($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setNamaCabang($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSisa($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSwReff($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setKodeLoket($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setNamaLoket($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setAlamatLoket($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPhoneLoket($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setKodeKabKota($value);
}
