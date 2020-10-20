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
 * Class CampaignDetailsMobile
 * @package SM\TodayDeal\Model
 */
class CampaignDetailsMobile extends AbstractModel implements \SM\TodayDeal\Api\Data\CampaignDetailsMobileInterface
{
    /**
     * @inheritDoc
     */
    public function getMbDescription()
    {
        return $this->getData(self::MB_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setMbDescription($value)
    {
        return $this->setData(self::MB_DESCRIPTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMbMostPopularProducts()
    {
        return $this->getData(self::MB_MOST_POPULAR_PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setMbMostPopularProducts($value)
    {
        return $this->setData(self::MB_MOST_POPULAR_PRODUCTS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMbSignatureProducts()
    {
        return $this->getData(self::MB_SIGNATURE_PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setMbSignatureProducts($value)
    {
        return $this->setData(self::MB_SIGNATURE_PRODUCTS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMbRelatedCampaignsBlockTitle()
    {
        return $this->getData(self::MB_RELATED_CAMPAIGNS_BLOCK_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setMbRelatedCampaignsBlockTitle($value)
    {
        return $this->setData(self::MB_RELATED_CAMPAIGNS_BLOCK_TITLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMbImageUrl()
    {
        return $this->getData(self::MB_IMAGE_URL);
    }

    /**
     * @inheritDoc
     */
    public function setMbImageUrl($value)
    {
        return $this->setData(self::MB_IMAGE_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMbVideoUrl()
    {
        return $this->getData(self::MB_VIDEO_URL);
    }

    /**
     * @inheritDoc
     */
    public function setMbVideoUrl($value)
    {
        return $this->setData(self::MB_VIDEO_URL);
    }

    /**
     * @inheritDoc
     */
    public function getMbSubCategories()
    {
        return $this->getData(self::MB_SUB_CATEGORIES);
    }

    /**
     * @inheritDoc
     */
    public function setMbSubCategories($value)
    {
        return $this->setData(self::MB_SUB_CATEGORIES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMbAllProductsCategory()
    {
        return $this->getData(self::MB_ALL_PRODUCTS_CATEGORY);
    }

    /**
     * @inheritDoc
     */
    public function setMbAllProductsCategory($value)
    {
        return $this->setData(self::MB_ALL_PRODUCTS_CATEGORY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMbRelatedCampaigns()
    {
        return $this->getData(self::MB_RELATED_CAMPAIGNS);
    }

    /**
     * @inheritDoc
     */
    public function setMbRelatedCampaigns($value)
    {
        return $this->setData(self::MB_RELATED_CAMPAIGNS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
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
    public function getMbSignatureTitle()
    {
        return $this->getData(self::MB_SIGNATURE_TITLE);
    }


    /**
     * @inheritDoc
     */
    public function getMbTrueTitle(){
        return $this->getData(self::MB_SIGNATURE_TRUE_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setMbTrueTitle($data){
        return $this->setData(self::MB_SIGNATURE_TRUE_TITLE,$data);
    }
}
