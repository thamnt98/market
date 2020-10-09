<?php

namespace SM\DigitalProduct\Model\Data\Inquire;

use SM\DigitalProduct\Api\Data\Inquire\InquireTelkomDataInterface;

/**
 * Class InquireTelkomData
 * @package SM\DigitalProduct\Model\Data\Inquire
 */
class InquireTelkomData extends ResponseData implements InquireTelkomDataInterface
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
    public function getDatetime()
    {
        return $this->getData(self::DATETIME);
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
    public function getProduk()
    {
        return $this->getData(self::PRODUK);
    }

    /**
     * @inheritDoc
     */
    public function getIdPelanggan()
    {
        return $this->getData(self::ID_PELANGGAN);
    }

    /**
     * @inheritDoc
     */
    public function getNamaPelanggan()
    {
        return $this->getData(self::NAMA_PELANGGAN);
    }

    /**
     * @inheritDoc
     */
    public function getNoReference()
    {
        return $this->getData(self::NO_REFERENCE);
    }

    /**
     * @inheritDoc
     */
    public function getBulanThn()
    {
        return $this->getData(self::BULAN_THN);
    }

    /**
     * @inheritDoc
     */
    public function getJumlahTagihan()
    {
        return $this->getData(self::JUMLAH_TAGIHAN);
    }

    /**
     * @inheritDoc
     */
    public function getJumlahAdm()
    {
        return $this->getData(self::JUMLAH_ADM);
    }

    /**
     * @inheritDoc
     */
    public function getJumlahBayar()
    {
        return $this->getData(self::JUMLAH_BAYAR);
    }

    /**
     * @inheritDoc
     */
    public function getRequestType()
    {
        return $this->getData(self::REQUEST_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
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
    public function setProductType($value)
    {
        return $this->setData(self::PRODUCT_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setProduk($value)
    {
        return $this->setData(self::PRODUK, $value);
    }

    /**
     * @inheritDoc
     */
    public function setIdPelanggan($value)
    {
        return $this->setData(self::ID_PELANGGAN, $value);
    }

    /**
     * @inheritDoc
     */
    public function setNamaPelanggan($value)
    {
        return $this->setData(self::NAMA_PELANGGAN, $value);
    }

    /**
     * @inheritDoc
     */
    public function setNoReference($value)
    {
        return $this->setData(self::NO_REFERENCE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBulanThn($value)
    {
        return $this->setData(self::BULAN_THN, $value);
    }

    /**
     * @inheritDoc
     */
    public function setJumlahTagihan($value)
    {
        return $this->setData(self::JUMLAH_TAGIHAN, $value);
    }

    /**
     * @inheritDoc
     */
    public function setJumlahAdm($value)
    {
        return $this->setData(self::JUMLAH_ADM, $value);
    }

    /**
     * @inheritDoc
     */
    public function setJumlahBayar($value)
    {
        return $this->setData(self::JUMLAH_BAYAR, $value);
    }

    /**
     * @inheritDoc
     */
    public function setRequestType($value)
    {
        return $this->setData(self::REQUEST_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }
}
