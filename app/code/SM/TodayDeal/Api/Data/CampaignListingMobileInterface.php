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
 * Interface CampaignListingMobileInterface
 * @package SM\TodayDeal\Api\Data
 */
interface CampaignListingMobileInterface
{
    const ID = 'post_id';
    const TITLE = 'title';
    const THUMBNAIL_URL = 'thumbnail_url';
    const PERIOD_DATE = 'period_date';
    const MB_SHORT_DESCRIPTION = 'mb_short_description';
    const POSITION = 'position';
    const PROMO_ID = 'promo_id';
    const PROMO_NAME = 'promo_name';
    const PROMO_CREATIVE = 'promo_creative';
    const TYPE = 'type';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setId($value);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setTitle($value);

    /**
     * @return string
     */
    public function getThumbnailUrl();

    /**
     * @param string $value
     * @return $this
     */
    public function setThumbnailUrl($value);

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
    public function getMbShortDescription();

    /**
     * @param string $value
     * @return $this
     */
    public function setMbShortDescription($value);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $value
     * @return $this
     */
    public function setPosition($value);

    /**
     * @return int
     */
    public function getPromoId();

    /**
     * @return string
     */
    public function getPromoName();

    /**
     * @return string
     */
    public function getPromoCreative();

    /**
     * @return int
     */
    public function getType();

    /**
     * @param int $value
     * @return $this
     */
    public function setType($value);
}
