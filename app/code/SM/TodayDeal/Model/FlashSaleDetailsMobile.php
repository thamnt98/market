<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\TodayDeal\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class FlashSaleDetailsMobile
 * @package SM\TodayDeal\Model
 */
class FlashSaleDetailsMobile extends AbstractModel implements \SM\TodayDeal\Api\Data\FlashSaleDetailsMobileInterface
{
    /**
     * @inheritDoc
     */
    public function getEventId()
    {
        return $this->getData(self::EVENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEventId($value)
    {
        return $this->setData(self::EVENT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryId($value)
    {
        return $this->setData(self::CATEGORY_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDateStart()
    {
        return $this->getData(self::DATE_START);
    }

    /**
     * @inheritDoc
     */
    public function setDateStart($value)
    {
        return $this->setData(self::DATE_START, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDateEnd()
    {
        return $this->getData(self::DATE_END);
    }

    /**
     * @inheritDoc
     */
    public function setDateEnd($value)
    {
        return $this->setData(self::DATE_END, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTermsConditions()
    {
        return $this->getData(self::TERMS_CONDITIONS);
    }

    /**
     * @inheritDoc
     */
    public function setTermsConditions($value)
    {
        return $this->setData(self::TERMS_CONDITIONS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getImageUrl()
    {
        return $this->getData(self::IMAGE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setProducts($value)
    {
        return $this->setData(self::PRODUCTS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTotalProducts()
    {
        return $this->getData(self::TOTAL_PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setTotalProducts($value)
    {
        return $this->setData(self::TOTAL_PRODUCTS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMbTitle()
    {
        return $this->getData(self::MB_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getPeriodDate()
    {
        return $this->getData(self::PERIOD_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setPeriodDate($value)
    {
        return $this->setData(self::PERIOD_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDateStartConverted()
    {
        // TODO: Implement getDateStartConverted() method.
        return $this->getData(self::DATE_START_CONVERTED);
    }

    /**
     * @inheritDoc
     */
    public function setDateStartConverted($value)
    {
        // TODO: Implement setDateStartConverted() method.
        $this->setData(self::DATE_START_CONVERTED,$value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDateEndConverted()
    {
        // TODO: Implement getDateEndConverted() method.
        return $this->getData(self::DATE_END_CONVERTED);
    }

    /**
     * @inheritDoc
     */
    public function setDateEndConverted($value)
    {
        // TODO: Implement setDateEndConverted() method.
        $this->setData(self::DATE_END_CONVERTED,$value);
        return $this;
    }
}
