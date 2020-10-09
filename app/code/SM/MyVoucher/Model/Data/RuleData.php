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

namespace SM\MyVoucher\Model\Data;

use Magento\Framework\DataObject;
use SM\MyVoucher\Api\Data\RuleDataInterface;

class RuleData extends DataObject implements RuleDataInterface
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @param $id
     * @return self
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param $name string
     * @return self
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);

    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param $description string
     * @return self
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return string
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * @param $discountAmount string
     * @return self
     */
    public function setDiscountAmount($discountAmount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);
    }

    /**
     * @return string
     */
    public function getDiscountType()
    {
        return $this->getData(self::DISCOUNT_TYPE);
    }

    /**
     * @param $discountType string
     * @return self
     */
    public function setDiscountType($discountType)
    {
        return $this->setData(self::DISCOUNT_TYPE, $discountType);
    }

    /**
     * @return string
     */
    public function getHowToUse()
    {
        return $this->getData(self::HOW_TO_USE);
    }

    /**
     * @param $howToUse string
     * @return self
     */
    public function setHowToUse($howToUse)
    {
        return $this->setData(self::HOW_TO_USE, $howToUse);
    }

    /**
     * @return string
     */
    public function getTermCondition()
    {
        return $this->getData(self::TERM_CONDITION);
    }

    /**
     * @param $termCondition string
     * @return self
     */
    public function setTermCondition($termCondition)
    {
        return $this->setData(self::TERM_CONDITION, $termCondition);
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @param $image string
     * @return self
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @return string
     */
    public function getUseLeft()
    {
        return $this->getData(self::USE_LEFT);
    }

    /**
     * @param $useLeft string
     * @return self
     */
    public function setUseLeft($useLeft)
    {
        return $this->setData(self::USE_LEFT, $useLeft);
    }

    /**
     * @return bool
     */
    public function getAvailable()
    {
        return $this->getData(self::AVAILABLE);
    }

    /**
     * @param $available bool
     * @return self
     */
    public function setAvailable($available)
    {
        return $this->setData(self::AVAILABLE, $available);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @param $code string
     * @return self
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @return string
     */
    public function getFromDate()
    {
        return $this->getData(self::FROM_DATE);
    }

    /**
     * @param $fromDate
     * @return self
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(self::FROM_DATE, $fromDate);
    }

    /**
     * @return string
     */
    public function getToDate()
    {
        return $this->getData(self::TO_DATE);
    }

    /**
     * @param $toDate
     * @return self
     */
    public function setToDate($toDate)
    {
        return $this->setData(self::TO_DATE, $toDate);
    }

    /**
     * @return string
     */
    public function getDiscountText()
    {
        return $this->getData(self::DISCOUNT_TEXT);
    }

    /**
     * @param string $discountText
     * @return self
     */
    public function setDiscountText($discountText)
    {
        return $this->setData(self::DISCOUNT_TEXT, $discountText);
    }

    /**
     * @return string
     */
    public function getDiscountNote()
    {
        return $this->getData(self::DISCOUNT_NOTE);
    }

    /**
     * @param string $discountNote
     * @return self
     */
    public function setDiscountNote($discountNote)
    {
        return $this->setData(self::DISCOUNT_NOTE, $discountNote);
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return $this->getData(self::AREA);
    }

    /**
     * @param string $area
     * @return self
     */
    public function setArea($area)
    {
        return $this->setData(self::AREA, $area);
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getData(self::REDIRECT_URL);
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setRedirectUrl($url)
    {
        return $this->setData(self::REDIRECT_URL, $url);
    }

    /**
     * @return string
     */
    public function isExpired()
    {
        return $this->getData(self::IS_EXPIRED);
    }

    /**
     * @param bool $value
     *
     * @return self
     */
    public function setIsExpired($value)
    {
        return $this->setData(self::IS_EXPIRED, $value);
    }

    /**
     * @return string
     */
    public function getMobileImage(){
        return $this->getData(self::MOBILE_IMAGE);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setMobileImage($data){
        return $this->setData(self::MOBILE_IMAGE,$data);
    }

    /**
     * @return string
     */
    public function getExpireDate(){
        return $this->getData(self::EXPIRE_DATE);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setExpireDate($data){
        return $this->setData(self::EXPIRE_DATE,$data);
    }


    /**
     * @return string
     */
    public function getMobileRedirect(){
        return $this->getData(self::MOBILE_REDIRECT);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setMobileRedirect($data){
        return $this->setData(self::MOBILE_REDIRECT,$data);
    }

    /**
     * @return string
     */
    public function getMobileArea(){
        switch ($this->getData(self::MOBILE_AREA)){
            case 1:return "Campaign"; break;
            case 2:return "Category"; break;
            case 3:return "ProductPage"; break;
            case 4:return "Campaign"; break;
            default: return ""; break;
        }
    }

    /**
     * @param int $data
     * @return $this
     */
    public function setMobileArea($data){
        return $this->setData(self::MOBILE_AREA,$data);
    }

    /**
     * @return int
     */
    public function getMobileAreaCode(){
        return $this->getData(self::MOBILE_AREA);
    }
}
