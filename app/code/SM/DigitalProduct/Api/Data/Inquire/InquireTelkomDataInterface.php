<?php

namespace SM\DigitalProduct\Api\Data\Inquire;

/**
 * Interface InquireTelkomDataInterface
 * @package SM\DigitalProduct\Api\Data\Inquire
 */
interface InquireTelkomDataInterface extends ResponseDataInterface
{
    const STAN = "stan";
    const DATETIME = "datetime";
    const PRODUCT_TYPE = "product_type";
    const PRODUK = "produk";
    const ID_PELANGGAN = "id_pelanggan";
    const NAMA_PELANGGAN = "nama_pelanggan";
    const NO_REFERENCE = "no_reference";
    const BULAN_THN = "bulan_thn";
    const JUMLAH_TAGIHAN = "jumlah_tagihan";
    const JUMLAH_ADM = "jumlah_adm";
    const JUMLAH_BAYAR = "jumlah_bayar";
    const REQUEST_TYPE = "request_type";
    const CODE = "code";

    /**
     * @return string
     */
    public function getStan();

    /**
     * @return string
     */
    public function getDatetime();

    /**
     * @return string
     */
    public function getProductType();

    /**
     * @return string
     */
    public function getProduk();

    /**
     * @return string
     */
    public function getIdPelanggan();

    /**
     * @return string
     */
    public function getNamaPelanggan();

    /**
     * @return string
     */
    public function getNoReference();

    /**
     * @return string
     */
    public function getBulanThn();

    /**
     * @return string
     */
    public function getJumlahTagihan();

    /**
     * @return string
     */
    public function getJumlahAdm();

    /**
     * @return string
     */
    public function getJumlahBayar();

    /**
     * @return string
     */
    public function getRequestType();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setStan($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDatetime($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setProductType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setProduk($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setIdPelanggan($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setNamaPelanggan($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setNoReference($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBulanThn($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setJumlahTagihan($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setJumlahAdm($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setJumlahBayar($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setRequestType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCode($value);
}
