<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\TodayDeal\Api\Data;

/**
 * Interface FlashSaleDetailsMobileInterface
 * @package SM\TodayDeal\Api\Data
 */
interface FlashSaleDetailsMobileInterface
{
    const EVENT_ID = 'event_id';
    const CATEGORY_ID = 'category_id';
    const DATE_START = 'date_start';
    const DATE_END = 'date_end';
    const DATE_START_CONVERTED = 'date_start_converted';
    const DATE_END_CONVERTED = 'date_end_converted';
    const TERMS_CONDITIONS = 'terms_conditions';
    const IMAGE_URL = 'image_url';
    const PRODUCTS = 'products';
    const TOTAL_PRODUCTS = 'total_products';
    const MB_TITLE = 'mb_title';
    const PERIOD_DATE = 'period_date';

    /**
     * @return int
     */
    public function getEventId();

    /**
     * @param int $value
     * @return $this
     */
    public function setEventId($value);

    /**
     * @return string
     */
    public function getMbTitle();

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $value
     * @return $this
     */
    public function setCategoryId($value);

    /**
     * @return string
     */
    public function getPeriodDate();

    /**
     * @param string $value
     * @return $this
     */
    public function setPeriodDate($value);

    /**
     * @return string
     */
    public function getDateStart();

    /**
     * @param string $value
     * @return $this
     */
    public function setDateStart($value);

    /**
     * @return string
     */
    public function getDateEnd();

    /**
     * @param string $value
     * @return $this
     */
    public function setDateEnd($value);

    /**
     * @return string
     */
    public function getTermsConditions();

    /**
     * @param string $value
     * @return $this
     */
    public function setTermsConditions($value);

    /**
     * @return string
     */
    public function getImageUrl();

    /**
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface[]
     */
    public function getProducts();

    /**
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface[] $value
     * @return $this
     */
    public function setProducts($value);

    /**
     * @return int
     */
    public function getTotalProducts();

    /**
     * @param int $value
     * @return $this
     */
    public function setTotalProducts($value);

    /**
     * @return string
     */
    public function getDateStartConverted();

    /**
     * @param string $value
     * @return $this
     */
    public function setDateStartConverted($value);

    /**
     * @return string
     */
    public function getDateEndConverted();

    /**
     * @param string $value
     * @return $this
     */
    public function setDateEndConverted($value);
}
