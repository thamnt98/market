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
 * Interface CampaignDetailsMobileInterface
 * @package SM\TodayDeal\Api\Data
 */
interface CampaignDetailsMobileInterface
{
    const TITLE = 'title';
    const PERIOD_DATE = 'period_date';
    const MB_DESCRIPTION = 'mb_description';
    const MB_MOST_POPULAR_PRODUCTS = 'mb_most_popular_products';
    const MB_SIGNATURE_PRODUCTS = 'mb_signature_products';
    const MB_RELATED_CAMPAIGNS_BLOCK_TITLE = 'mb_related_campaigns_block_title';
    const MB_IMAGE_URL = 'mb_image_url';
    const MB_VIDEO_URL = 'mb_video_url';
    const MB_SUB_CATEGORIES = 'mb_sub_categories';
    const MB_ALL_PRODUCTS_CATEGORY = 'mb_all_products_category';
    const MB_RELATED_CAMPAIGNS = 'mb_related_campaigns';
    const MB_SIGNATURE_TITLE = 'mb_signature_title';
    const MB_SIGNATURE_TRUE_TITLE = 'mb_signature_true_title';

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getPeriodDate();

    /**
     * @return string
     */
    public function getMbDescription();

    /**
     * @param string $value
     * @return $this
     */
    public function setMbDescription($value);

    /**
     * @return string
     */
    public function getMbSignatureTitle();

    /**
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface[] $value
     * @return $this
     */
    public function setMbMostPopularProducts($value);

    /**
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface[]
     */
    public function getMbSignatureProducts();

    /**
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface[] $value
     * @return $this
     */
    public function setMbSignatureProducts($value);

    /**
     * @return string
     */
    public function getMbRelatedCampaignsBlockTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setMbRelatedCampaignsBlockTitle($value);

    /**
     * @return string
     */
    public function getMbImageUrl();

    /**
     * @param string $value
     * @return $this
     */
    public function setMbImageUrl($value);

    /**
     * @return string
     */
    public function getMbVideoUrl();

    /**
     * @param string $value
     * @return $this
     */
    public function setMbVideoUrl($value);

    /**
     * @return int
     */
    public function getMbAllProductsCategory();

    /**
     * @param int $value
     * @return $this
     */
    public function setMbAllProductsCategory($value);

    /**
     * @return \SM\TodayDeal\Api\Data\CampaignListingMobileInterface[]
     */
    public function getMbRelatedCampaigns();

    /**
     * @param \SM\TodayDeal\Api\Data\CampaignListingMobileInterface[] $value
     * @return $this
     */
    public function setMbRelatedCampaigns($value);

    /**
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface[]
     */
    public function getMbMostPopularProducts();

    /**
     * @return \SM\Category\Api\Data\Catalog\CategoryInterface[]
     */
    public function getMbSubCategories();

    /**
     * @param \SM\Category\Api\Data\Catalog\CategoryInterface[] $value
     * @return $this
     */
    public function setMbSubCategories($value);

    /**
     * @return string
     */
    public function getMbTrueTitle();

    /**
     * @param string $data
     * @return $this
     */
    public function setMbTrueTitle($data);
}
