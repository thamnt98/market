<?php
/**
 * @category SM
 * @package  SM_MyVoucher
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hung Pham <hungpv@smartosc.com>
 *
 * Copyright © 2020 SmartOSC. All rights reserved.
 * http://www.smartosc.com
 */

namespace SM\MyVoucher\Api\Data;

interface RuleDataInterface
{
    const ID = 'coupon_id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const DISCOUNT_TYPE = 'discount_type';
    const HOW_TO_USE = 'how_to_use';
    const TERM_CONDITION = 'term_condition';
    const IMAGE = 'image';
    const USE_LEFT = 'use_left';
    const AVAILABLE = 'available';
    const CODE = 'code';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const DISCOUNT_TEXT = 'discount_text';
    const DISCOUNT_NOTE = 'discount_note';
    const AREA = 'area';
    const REDIRECT_URL = 'front_redirect_url';
    const IS_EXPIRED = 'is_expired';
    const MOBILE_IMAGE = 'mobile_image';
    const EXPIRE_DATE = 'expire_date';
    const MOBILE_REDIRECT = 'mobile_redirect';
    const MOBILE_AREA = 'mobile_redirect_area';
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $id
     * @return self
     */
    public function setId($id);
    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name string
     * @return self
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $description string
     * @return self
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDiscountAmount();

    /**
     * @param $discountAmount string
     * @return self
     */
    public function setDiscountAmount($discountAmount);

    /**
     * @return string
     */
    public function getDiscountType();

    /**
     * @param $discountType string
     * @return self
     */
    public function setDiscountType($discountType);

    /**
     * @return string
     */
    public function getHowToUse();

    /**
     * @param $howToUse string
     * @return self
     */
    public function setHowToUse($howToUse);

    /**
     * @return string
     */
    public function getTermCondition();

    /**
     * @param $termCondition string
     * @return self
     */
    public function setTermCondition($termCondition);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param $image string
     * @return self
     */
    public function setImage($image);

    /**
     * @return int
     */
    public function getUseLeft();

    /**
     * @param int $useLeft
     * @return self
     */
    public function setUseLeft($useLeft);

    /**
     * @return bool
     */
    public function getAvailable();

    /**
     * @param $available bool
     * @return self
     */
    public function setAvailable($available);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param $code string
     * @return self
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getFromDate();

    /**
     * @param string $fromDate
     * @return self
     */
    public function setFromDate($fromDate);

    /**
     * @return string
     */
    public function getToDate();

    /**
     * @param string $toDate
     * @return self
     */
    public function setToDate($toDate);

    /**
     * @return string
     */
    public function getDiscountText();

    /**
     * @param string $discountText
     * @return self
     */
    public function setDiscountText($discountText);

    /**
     * @return string
     */
    public function getDiscountNote();

    /**
     * @param string $discountNote
     * @return self
     */
    public function setDiscountNote($discountNote);

    /**
     * @return string
     */
    public function getArea();

    /**
     * @param string $area
     * @return self
     */
    public function setArea($area);

    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @param string $url
     *
     * @return self
     */
    public function setRedirectUrl($url);

    /**
     * @return bool
     */
    public function isExpired();

    /**
     * @param bool $value
     *
     * @return self
     */
    public function setIsExpired($value);

    /**
     * @return string
     */
    public function getMobileImage();

    /**
     * @param string $data
     * @return $this
     */
    public function setMobileImage($data);

    /**
     * @return string
     */
    public function getExpireDate();

    /**
     * @param string $data
     * @return $this
     */
    public function setExpireDate($data);

    /**
     * @return string
     */
    public function getMobileRedirect();

    /**
     * @param string $data
     * @return $this
     */
    public function setMobileRedirect($data);

    /**
     * @return string
     */
    public function getMobileArea();

    /**
     * @param int $data
     * @return $this
     */
    public function setMobileArea($data);

    /**
     * @return int
     */
    public function getMobileAreaCode();
}
