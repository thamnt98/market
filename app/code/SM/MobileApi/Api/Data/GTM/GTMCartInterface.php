<?php

namespace SM\MobileApi\Api\Data\GTM;

/**
 * @api
 * Interface ListInterface
 * @package SM\MobileApi\Api\Data\Product
 */
interface GTMCartInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRODUCT_NAME = 'product_name';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_PRICE = 'product_price';
    const PRODUCT_BRAND = 'product_brand';
    const PRODUCT_CATEGORY = 'product_category';
    const PRODUCT_VARIANT = 'product_variant';
    const PRODUCT_LIST = 'product_list';
    const PRODUCT_SIZE = 'product_size';
    const PRODUCT_VOLUME = 'product_volume';
    const PRODUCT_WEIGHT = 'product_weight';
    const DISCOUNT_PRICE = 'discount_price';
    const INITIAL_PRICE = 'initial_price';
    const DISCOUNT_RATE = 'discount_rate';

    const PRODUCT_RATING = 'product_rating';
    const PRODUCT_ON_SALE = 'product_on_sale';
    const PRODUCT_BUNDLE = 'product_bundle';
    const PRODUCT_QTY = 'product_qty';
    const PRODUCT_TYPE = 'product_type';
    const APPLY_VOUCHER = 'apply_voucher';
    const VOUCHER_ID = 'voucher_id';
    const SALE_PRICE = 'sale_price';


    /**
     * @return string
     */
    public function getProductName();

    /**
     * @param string $value
     * @return $this
     */
    public function setProductName($value);

    /**
     * @return string
     */
    public function getProductId();

    /**
     * @param string $value
     * @return $this
     */
    public function setProductId($value);

    /**
     * @return string
     */
    public function getProductPrice();

    /**
     * @param string $value
     * @return $this
     */
    public function setProductPrice($value);

    /**
     * @return string
     */
    public function getProductCategory();

    /**
     * @param string $value
     * @return $this
     */
    public function setProductCategory($value);

    /**
     * @return string
     */
    public function getProductSize();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductSize($data);

    /**
     * @return string
     */
    public function getProductVolume();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductVolume($data);

    /**
     * @return string
     */
    public function getProductWeight();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductWeight($data);

    /**
     * @return string
     */
    public function getProductBrand();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductBrand($data);

    /**
     * @return string
     */
    public function getProductVariant();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductVariant($data);

    /**
     * @return string
     */
    public function getDiscountPrice();

    /**
     * @param string $data
     * @return $this
     */
    public function setDiscountPrice($data);

    /**
     * @return string
     */
    public function getProductList();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductList($data);

    /**
     * @return string
     */
    public function getInitialPrice();

    /**
     * @param string $data
     * @return $this
     */
    public function setInitialPrice($data);

    /**
     * @return string
     */
    public function getDiscountRate();

    /**
     * @param string $data
     * @return $this
     */
    public function setDiscountRate($data);

    /**
     * @return string
     */
    public function getProductRating();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductRating($data);

    /**
     * @return string
     */
    public function getProductOnSale();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductOnSale($data);

    /**
     * @return string
     */
    public function getProductBundle();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductBundle($data);

    /**
     * @return int
     */
    public function getProductQty();

    /**
     * @param int $data
     * @return $this
     */
    public function setProductQty($data);

    /**
     * @return string
     */
    public function getProductType();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductType($data);

    /**
     * @return string
     */
    public function getApplyVoucher();

    /**
     * @param string $data
     * @return $this
     */
    public function setApplyVoucher($data);

    /**
     * @return string
     */
    public function getVoucherId();

    /**
     * @param string $data
     * @return $this
     */
    public function setVoucherId($data);

    /**
     * @return float
     */
    public function getSalePrice();

    /**
     * @param float $data
     * @return $this
     */
    public function setSalePrice($data);
}
