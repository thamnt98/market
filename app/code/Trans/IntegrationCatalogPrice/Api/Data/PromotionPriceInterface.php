<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Api\Data;

interface PromotionPriceInterface
{
    /**
     * Constant for table name
     */
    const DEFAULT_EVENT = 'trans_integration';
    const TABLE_NAME    = 'integration_catalog_promotion_price';

    /**
     * Constant for field name
     */
    const ID                        = 'id';
    const PIM_STORECODE             = 'store_code';
    const PIM_SKU                   = 'sku';
    const STATUS_JOB_DATA           = 1;
    const PIM_ID                    = "pim_id";
    const PIM_NAME                  = "name";
    const PIM_COMPANY_CODE          = "company_code";
    const PIM_PRODUCT_ID            = "pim_product_id";
    const PIM_PROMOTION_ID          = "pim_promotion_id";
    const CREATED_AT                = 'created_at';
    const UPDATED_AT                = 'updated_at';
    const PIM_PROMOTION_TYPE        = 'promotion_type';
    const PIM_DISCOUNT_TYPE         = 'discount_type';
    const PIM_MIX_MATCH_CODE        = 'mix_match_code';
    const PIM_SLIDING_DISC_TYPE     = 'sliding_disc_type';
    const PIM_SALESRULE_ID          = 'salesrule_id';
    const PIM_ITEM_TYPE             = 'item_type';
    const PIM_ROW_ID                = 'row_id';
    const PIM_REQUIRED_POINT        = 'required_point';
    const PIM_PROMO_SELLING_PRICE   = 'promo_selling_price';
    const PIM_PERCENT_DISC          = 'percent_disc';
    const PIM_AMOUNT_OFF            = 'amount_off';

    /**
     * get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set Id
     *
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set Sku
     *
     * @param string $sku
     * @return void
     */
    public function setSku($sku);

    /**
     * Get StoreCode
     *
     * @return string
     */
    public function getStoreCode();

    /**
     * Set StoreCode
     *
     * @param string $code
     * @return void
     */
    public function setStoreCode($code);

    /**
     * Get PIM Company CODE
     *
     * @return string
     */
    public function getCompanyCode();

    /**
     * Set PIM Company CODE
     *
     * @param string $code
     * @return void
     */
    public function setCompanyCode($code);

    /**
     * Get PIM Product Id
     *
     * @return string
     */
    public function getPimProductId();

    /**
     * Set PIM Product Id
     *
     * @param string $id
     * @return void
     */
    public function setPimProductId($id);

    /**
     * Get PIM Promotion Id
     *
     * @return string
     */
    public function getPimPromotionId();

    /**
     * Set PIM Promotion Id
     *
     * @param string $id
     * @return void
     */
    public function setPimPromotionId($id);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get pim id
     *
     * @return string
     */
    public function getPimId();

    /**
     * Set pim id
     *
     * @param string $pimid
     * @return void
     */
    public function setPimId($pimid);

    /**
     * Get pim name
     *
     * @return string
     */
    public function getName();

    /**
     * Set pim name
     *
     * @param string $pimname
     * @return void
     */
    public function setName($name);

    /**
     * Get pim promotion type
     *
     * @return string
     */
    public function getPromotionType();

    /**
     * Set pim promotion type
     *
     * @param string $promotype
     * @return void
     */
    public function setPromotionType($promotype);

    /**
     * Get pim discount type
     *
     * @return string
     */
    public function getDiscountType();

    /**
     * Set pim discount type
     *
     * @param string $discounttype
     * @return void
     */
    public function setDiscountType($discounttype);

    /**
     * Get pim item type
     *
     * @return string
     */
    public function getItemType();

    /**
     * Set pim item type
     *
     * @param string $discounttype
     * @return void
     */
    public function setItemType($itemtype);

    /**
     * Get pim mix match code
     *
     * @return string
     */
    public function getMixMatchCode();

    /**
     * Set pim mix match code
     *
     * @param string $code
     * @return void
     */
    public function setMixMatchCode($code);

    /**
     * Get pim sliding_disc_type
     *
     * @return string
     */
    public function getSlidingDiscType();

    /**
     * Set pim sliding_disc_type
     *
     * @param string $code
     * @return void
     */
    public function setSlidingDiscType($code);

    /**
     * Get pim salesrule id
     *
     * @return string
     */
    public function getSaleruleId();

    /**
     * Set pim salesrule id
     *
     * @param string $id
     * @return void
     */
    public function setSaleruleId($id);

    /**
     * get amount_off
     *
     * @return int
     */
    public function getAmountOff();

    /**
     * Set amount_off
     *
     * @param int $amount
     * @return void
     */
    public function setAmountOff($amount);

    /**
     * get percent_disc
     *
     * @return int
     */
    public function getPercentDisc();

    /**
     * Set percent_disc
     *
     * @param int $disc
     * @return void
     */
    public function setPercentDisc($disc);

    /**
     * get promo_selling_price
     *
     * @return int
     */
    public function getPromoSellingPrice();

    /**
     * Set promo_selling_price
     *
     * @param int $point
     * @return void
     */
    public function setPromoSellingPrice($point);

    /**
     * get required_point
     *
     * @return int
     */
    public function getRequiredPoint();

    /**
     * Set required_point
     *
     * @param int $point
     * @return void
     */
    public function setRequiredPoint($point);

    /**
     * get row_id
     *
     * @return int
     */
    public function getRowId();

    /**
     * Set row_id
     *
     * @param int $id
     * @return void
     */
    public function setRowId($id);
}
