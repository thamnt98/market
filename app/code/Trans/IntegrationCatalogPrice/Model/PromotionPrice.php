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

namespace Trans\IntegrationCatalogPrice\Model;

use \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface;
use \Trans\IntegrationCatalogPrice\Model\ResourceModel\PromotionPrice as ResourceModel;

class PromotionPrice extends \Magento\Framework\Model\AbstractModel implements PromotionPriceInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_getData(PromotionPriceInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(PromotionPriceInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getSku()
    {
        return $this->_getData(PromotionPriceInterface::PIM_SKU);
    }

    /**
     * @inheritdoc
     */
    public function setSku($sku)
    {
        $this->setData(PromotionPriceInterface::PIM_SKU, $sku);
    }

    /**
     * @inheritdoc
     */
    public function getStoreCode()
    {
        return $this->_getData(PromotionPriceInterface::PIM_STORECODE);
    }

    /**
     * @inheritdoc
     */
    public function setStoreCode($code)
    {
        $this->setData(PromotionPriceInterface::PIM_STORECODE, $code);
    }
    
    /**
     * @inheritdoc
     */
    public function getCompanyCode()
    {
        return $this->_getData(PromotionPriceInterface::PIM_COMPANY_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setCompanyCode($code)
    {
        $this->setData(PromotionPriceInterface::PIM_COMPANY_CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getPimProductId()
    {
        return $this->_getData(PromotionPriceInterface::PIM_PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPimProductId($id)
    {
        $this->setData(PromotionPriceInterface::PIM_PRODUCT_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getPimPromotionId()
    {
        return $this->_getData(PromotionPriceInterface::PIM_PROMOTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPimPromotionId($id)
    {
        $this->setData(PromotionPriceInterface::PIM_PROMOTION_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(PromotionPriceInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(PromotionPriceInterface::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(PromotionPriceInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(PromotionPriceInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getPimId()
    {
        return $this->_getData(PromotionPriceInterface::PIM_ID);
    }

    /**
      * @inheritdoc
      */
    public function setPimId($pimid)
    {
        $this->setData(PromotionPriceInterface::PIM_ID, $pimid);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(PromotionPriceInterface::PIM_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(PromotionPriceInterface::PIM_NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getPromotionType()
    {
        return $this->_getData(PromotionPriceInterface::PIM_PROMOTION_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setPromotionType($promotype)
    {
        $this->setData(PromotionPriceInterface::PIM_PROMOTION_TYPE, $promotype);
    }

    /**
     * @inheritdoc
     */
    public function getDiscountType()
    {
        return $this->_getData(PromotionPriceInterface::PIM_DISCOUNT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountType($discounttype)
    {
        $this->setData(PromotionPriceInterface::PIM_DISCOUNT_TYPE, $discounttype);
    }

    /**
     * @inheritdoc
     */
    public function getItemType()
    {
        return $this->_getData(PromotionPriceInterface::PIM_ITEM_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setItemType($itemtype)
    {
        $this->setData(PromotionPriceInterface::PIM_ITEM_TYPE, $itemtype);
    }

    /**
     * @inheritdoc
     */
    public function getMixMatchCode()
    {
        return $this->_getData(PromotionPriceInterface::PIM_MIX_MATCH_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setMixMatchCode($code)
    {
        $this->setData(PromotionPriceInterface::PIM_MIX_MATCH_CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getSlidingDiscType()
    {
        return $this->_getData(PromotionPriceInterface::PIM_SLIDING_DISC_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setSlidingDiscType($code)
    {
        $this->setData(PromotionPriceInterface::PIM_SLIDING_DISC_TYPE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getSaleruleId()
    {
        return $this->_getData(PromotionPriceInterface::PIM_SALESRULE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSaleruleId($id)
    {
        $this->setData(PromotionPriceInterface::PIM_SALESRULE_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getRowId()
    {
        return $this->_getData(PromotionPriceInterface::PIM_ROW_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRowId($id)
    {
        $this->setData(PromotionPriceInterface::PIM_ROW_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getRequiredPoint()
    {
        return $this->_getData(PromotionPriceInterface::PIM_REQUIRED_POINT);
    }

    /**
     * @inheritdoc
     */
    public function setRequiredPoint($point)
    {
        $this->setData(PromotionPriceInterface::PIM_REQUIRED_POINT, $point);
    }

    /**
     * @inheritdoc
     */
    public function getPromoSellingPrice()
    {
        return $this->_getData(PromotionPriceInterface::PIM_REQUIRED_POINT);
    }

    /**
     * @inheritdoc
     */
    public function setPromoSellingPrice($point)
    {
        $this->setData(PromotionPriceInterface::PIM_PROMO_SELLING_PRICE, $point);
    }

    /**
     * @inheritdoc
     */
    public function getPercentDisc()
    {
        return $this->_getData(PromotionPriceInterface::PIM_PERCENT_DISC);
    }

    /**
     * @inheritdoc
     */
    public function setPercentDisc($disc)
    {
        $this->setData(PromotionPriceInterface::PIM_PERCENT_DISC, $disc);
    }

    /**
     * @inheritdoc
     */
    public function getAmountOff()
    {
        return $this->_getData(PromotionPriceInterface::PIM_AMOUNT_OFF);
    }

    /**
     * @inheritdoc
     */
    public function setAmountOff($amount)
    {
        $this->setData(PromotionPriceInterface::PIM_AMOUNT_OFF, $amount);
    }

    /**
     * @inheritdoc
     */
    public function getPointPerUnit()
    {
        return $this->_getData(PromotionPriceInterface::PIM_POINT_PER_UNIT);
    }

    /**
     * @inheritdoc
     */
    public function setPointPerUnit($point)
    {
        $this->setData(PromotionPriceInterface::PIM_POINT_PER_UNIT, $point);
    }
}
